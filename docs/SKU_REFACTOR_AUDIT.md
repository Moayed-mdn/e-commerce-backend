# SKU Architecture Refactor: Audit & Migration Plan

## Executive Summary

The current system has **dual SKU storage** (both `products.sku` and `product_variants.sku`), creating architectural inconsistency. The codebase has already evolved toward a variant-first architecture where:

- **Product** = abstract container (shared attributes, translations, categorization)
- **Variant** = actual purchasable entity (SKU, price, inventory)

This document provides a categorized audit and phased migration strategy.

---

## 1. Current State Analysis

### Database Schema

| Table | SKU Field | Status |
|-------|-----------|--------|
| `products` | `sku` (unique, nullable) | **DEPRECATED** - Not populated by seeders |
| `product_variants` | `sku` (unique, NOT NULL) | **SOURCE OF TRUTH** - Actively used |
| `order_items` | `sku` (string, NOT NULL) | **SNAPSHOT** - Captured at order time |

### Current Architecture Strengths

1. **Every product has at least one variant** (`CreateProductRequest` enforces `variants.min:1`)
2. **Products have `product_variant_id`** referencing the default/primary variant
3. **All inventory operations target variants** (quantity, price, stock checks)
4. **Order items reference variants** via `product_variant_id`

---

## 2. SKU Usage Audit

### 2.1 Places CORRECTLY Using Variant SKU (No Changes Needed)

| File | Line | Usage |
|------|------|-------|
| `app/Actions/Admin/Product/CreateProductAction.php:44` | Variant creation | SKU from DTO |
| `app/Actions/Order/CreateOrderAction.php:62` | Order item SKU | From `$variant->sku` |
| `app/Http/Resources/ProductResource.php:51` | API response | `$v->sku` |
| `app/Http/Resources/CartItemResource.php:64` | Cart response | `$variant->sku` |
| `app/Http/Resources/OrderItemResource.php:42` | Order response | `$this->sku` (captured) |
| `app/Http/Resources/ProductDetailResource.php:111` | Product detail | `$variant->sku` |
| `app/Http/Resources/ProductVariantResource.php:13` | Variant resource | `$this->sku` |
| `app/Http/Resources/Admin/Product/AdminProductDetailResource.php:56,66` | Admin detail | `$defaultVariant?->sku`, `$v->sku` |
| `database/seeders/ProductSeeder.php:341` | Seeding | Variant SKU generation |
| `database/seeders/FakeSalesSeeder.php:78` | Order seeding | `$variant->sku` |
| `database/factories/ProductVariantFactory.php:25` | Factory | Variant SKU generation |

**Verdict:** These correctly treat variant as SKU owner. **No changes required.**

### 2.2 Places Using Product SKU (BREAKING POINTS)

| File | Line | Usage | Risk Level |
|------|------|-------|------------|
| `app/Repositories/Search/SearchRepository.php:25,53` | Product search by SKU | **HIGH** |

```php
// Line 25, 53 - Searching products by products.sku
->orWhere('sku', 'LIKE', "%{$query}%");
```

**Issue:** Search looks at `products.sku` which is NULL for all seeded products. This breaks SKU-based search.

### 2.3 Dangerous Breaking Points

#### A. Search Repository (HIGH RISK)
**File:** `app/Repositories/Search/SearchRepository.php`

```php
// Current (broken for SKU search)
->orWhere('sku', 'LIKE', "%{$query}%");

// Should search variants and join to products
->orWhereHas('variants', function ($sq) use ($query) {
    $sq->where('sku', 'LIKE', "%{$query}%");
})
```

**Impact:** SKU search returns no results (products.sku is NULL).

#### B. Product Model Missing SKU Accessor (MEDIUM RISK)
**File:** `app/Models/Product.php`

**Issue:** If any legacy code or API client expects `$product->sku`, it will return NULL.

**Solution:** Add computed accessor:
```php
public function getSkuAttribute(): ?string
{
    return $this->defaultVariant?->sku ?? $this->variants->first()?->sku;
}
```

---

## 3. Architecture Gaps

### 3.1 Missing: Primary Variant Helper Methods

The Product model has `defaultVariant()` relation but lacks semantic helpers:

```php
// MISSING: Should add to Product model
public function primaryVariant(): ?ProductVariant
{
    return $this->defaultVariant ?? $this->variants->first();
}
```

### 3.2 Missing: SKU Uniqueness Validation Scope

**File:** `app/Http/Requests/Admin/Product/CreateProductRequest.php:33`

Current validation:
```php
'variants.*.sku' => ['required', 'string', 'max:100'],
```

**Gap:** No store-scoped uniqueness validation. Two products in same store could have variants with same SKU.

### 3.3 Missing: Automatic Default Variant Assignment

**File:** `app/Actions/Admin/Product/CreateProductAction.php`

Current code creates variants but doesn't set `product_variant_id` on the product.

**Gap:** Products created via API won't have a default variant set, breaking `primaryVariant()` logic.

### 3.4 Incomplete UpdateProductAction

**File:** `app/Actions/Admin/Product/UpdateProductAction.php:44-47`

```php
if (!is_null($dto->variants)) {
    // For simplicity in this audit pass, we assume full variant replacement 
    // or specific logic. Real implementation might be more complex.
}
```

**Gap:** Variant updates are not implemented. This blocks SKU updates for existing products.

---

## 4. Migration Strategy

### Phase 1: Foundation (Backward Compatible)

**Goal:** Establish variant as SKU source of truth while preserving compatibility.

#### 4.1.1 Add Product SKU Accessor (BACKWARD COMPATIBLE)
**File:** `app/Models/Product.php`

```php
/**
 * Get SKU from primary variant (backward compatibility).
 * @deprecated Use primaryVariant()->sku instead.
 */
public function getSkuAttribute(): ?string
{
    return $this->primaryVariant()?->sku;
}

/**
 * Get the primary/default variant for this product.
 */
public function primaryVariant(): ?ProductVariant
{
    // Try explicitly set default first
    if ($this->product_variant_id && $this->defaultVariant) {
        return $this->defaultVariant;
    }
    
    // Fall back to first active variant
    return $this->activeVariants->first() ?? $this->variants->first();
}
```

#### 4.1.2 Fix Search Repository (BUG FIX)
**File:** `app/Repositories/Search/SearchRepository.php`

```php
// Replace:
->orWhere('sku', 'LIKE', "%{$query}%")

// With:
->orWhereHas('variants', function ($sq) use ($query) {
    $sq->where('sku', 'LIKE', "%{$query}%");
})
```

#### 4.1.3 Ensure Default Variant Assignment
**File:** `app/Actions/Admin/Product/CreateProductAction.php`

Add after variant creation loop:
```php
// Set first variant as default
if ($firstVariant = $product->variants->first()) {
    $product->update(['product_variant_id' => $firstVariant->id]);
}
```

### Phase 2: Validation & Constraints

#### 4.2.1 Add SKU Uniqueness Validation
**File:** `app/Http/Requests/Admin/Product/CreateProductRequest.php` and `UpdateProductRequest.php`

```php
use Illuminate\Validation\Rule;

'variants.*.sku' => [
    'required', 
    'string', 
    'max:100',
    Rule::unique('product_variants', 'sku')->where(function ($query) use ($storeId) {
        // Scope uniqueness to store via product relationship
        $query->whereHas('product', function ($q) use ($storeId) {
            $q->where('store_id', $storeId);
        });
    })->ignore($variantId, 'id') // For updates
],
```

#### 4.2.2 Implement Variant Update Logic
**File:** `app/Actions/Admin/Product/UpdateProductAction.php`

Implement the missing variant sync/update logic.

### Phase 3: API Refactoring (Frontend Coordination Required)

#### 4.3.1 Product Response Structure
Current: SKU nested in variants (correct)
Target: Same structure, ensure frontend uses `variant.sku`

**Files to Review:**
- `app/Http/Resources/ProductResource.php` ✓ (already correct)
- `app/Http/Resources/ProductDetailResource.php` ✓ (already correct)
- `app/Http/Resources/Admin/Product/AdminProductDetailResource.php` ✓ (already correct)

**Action:** Document API structure for frontend team.

### Phase 4: Database Cleanup (FUTURE - After Frontend Migration)

#### 4.4.1 Deprecate products.sku
```php
// Migration (for future Phase 3)
Schema::table('products', function (Blueprint $table) {
    $table->dropUnique(['sku']);
    $table->dropColumn('sku');
});
```

**Prerequisites before Phase 4:**
- [ ] All frontend code uses variant SKU
- [ ] All admin dashboards use variant SKU
- [ ] Reports/analytics use variant SKU
- [ ] Mobile apps updated

---

## 5. File-by-File Implementation Order

### Priority 1: Critical Fixes (Do First)
1. `app/Models/Product.php` - Add `primaryVariant()` and `getSkuAttribute()`
2. `app/Repositories/Search/SearchRepository.php` - Fix SKU search
3. `app/Actions/Admin/Product/CreateProductAction.php` - Set default variant

### Priority 2: Validation & Safety
4. `app/Http/Requests/Admin/Product/CreateProductRequest.php` - SKU uniqueness
5. `app/Http/Requests/Admin/Product/UpdateProductRequest.php` - SKU uniqueness
6. `app/Actions/Admin/Product/UpdateProductAction.php` - Implement variant updates

### Priority 3: Documentation
7. `docs/ARCHITECTURE.md` - Document variant-first architecture

### Priority 4: Future Cleanup (Coordinate with Frontend)
8. Migration to drop `products.sku` column

---

## 6. Testing Checklist

### After Phase 1 Implementation:
- [ ] Search by SKU returns products with matching variant SKU
- [ ] `$product->sku` returns variant SKU (backward compatibility)
- [ ] New products get `product_variant_id` set automatically
- [ ] Cart operations still work correctly
- [ ] Order creation captures variant SKU correctly
- [ ] Admin product creation works

### After Phase 2 Implementation:
- [ ] Cannot create duplicate SKU in same store
- [ ] Can update product variants via admin API
- [ ] SKU validation errors return proper messages

---

## 7. API Response Structure (Document for Frontend)

### Product List Response
```json
{
  "id": 1,
  "name": "T-Shirt",
  "variants": [
    {
      "id": 101,
      "sku": "TSHIRT-RED-L",
      "price": 29.99,
      "quantity": 50
    }
  ]
}
```

### Product Detail Response
```json
{
  "id": 1,
  "name": "T-Shirt",
  "default_variant_id": 101,
  "variants": [
    {
      "id": 101,
      "sku": "TSHIRT-RED-L",
      "price": 29.99,
      "stock": 50
    }
  ]
}
```

### Order Item Response
```json
{
  "product_variant_id": 101,
  "product_name": "T-Shirt",
  "sku": "TSHIRT-RED-L",
  "unit_price": 29.99
}
```

**Note:** SKU is always at the **variant level**, never at the product root level.

---

## 8. Summary

### Current State
- ✅ Variant-first architecture is mostly implemented
- ✅ Inventory/stock correctly on variants
- ✅ Order items reference variants
- ⚠️ Search by SKU is broken (looks at products.sku)
- ⚠️ Missing backward compatibility accessor
- ⚠️ Missing default variant assignment on create

### Migration Risk Level: **LOW**

The system is already architected correctly. Changes needed:
1. Fix search (1 line change)
2. Add backward compatibility accessor (10 lines)
3. Ensure default variant assignment (3 lines)
4. Add validation rules (enhancement)

### Recommended Timeline
- **Phase 1:** 1 day (immediate)
- **Phase 2:** 1 day (after Phase 1 tested)
- **Phase 3:** Coordinate with frontend (1-2 weeks)
- **Phase 4:** After frontend confirmed migrated (future sprint)
