# Laravel Multi-Tenant E-commerce Architecture Audit Report

## Executive Summary

This report documents the audit findings and required fixes for transitioning from a "simple product" architecture to a strict "variant-first" architecture similar to Shopify. The audit was conducted against the rules defined in `/docs/ARCHITECTURE.md`.

**Audit Date:** 2025-05-10  
**Auditor:** AI Code Expert  
**Status:** PHASE 1 (SAFE IMMEDIATE FIXES) - In Progress

---

## Critical Issues Identified

### Issue #1: Slug Uniqueness is NOT Store-Scoped 🔴 CRITICAL

**Location:** `database/migrations/2025_10_09_140030_create_product_translations_table.php`

**Current State:**
```php
$table->unique(['slug', 'locale']);
```

**Problem:**
- Store A and Store B cannot both use slug `iphone-14` with locale `en`
- Cross-tenant slug leakage possible
- Violates multi-tenant isolation principle

**Required Fix:**
```php
$table->unique(['slug', 'locale', 'store_id']);
```

**Migration Plan:**
1. Add `store_id` column to `product_translations` table
2. Backfill `store_id` from `products` table via JOIN
3. Drop old unique constraint
4. Add new unique constraint with `store_id`

**Backward Compatibility:** 
- Requires careful migration to avoid breaking existing slugs
- Frontend URLs using slugs will continue to work if store context is maintained

---

### Issue #2: SKU Uniqueness is NOT Store-Scoped 🔴 CRITICAL

**Location:** `database/migrations/2025_10_09_140107_create_product_variants_table.php`

**Current State:**
```php
$table->string('sku')->unique();
```

**Problem:**
- All stores share one global SKU namespace
- Store A cannot have SKU "TSHIRT-001" if Store B already has it
- Violates multi-tenant isolation

**Required Fix:**
```php
$table->string('sku');
// ... later add:
$table->unique(['sku', 'store_id']);
```

**Migration Plan:**
1. Add `store_id` column to `product_variants` table
2. Backfill `store_id` from `products` table via JOIN
3. Drop old unique constraint
4. Add new unique constraint with `store_id`

**Backward Compatibility:**
- Existing SKUs remain valid
- New validation ensures store-scoped uniqueness

---

### Issue #3: Product Update Action is DESTRUCTIVE 🔴 CRITICAL

**Location:** `app/Actions/Admin/Product/UpdateProductAction.php` (lines 44-70)

**Current State:**
```php
if (!is_null($dto->variants)) {
    // Sync variants: delete existing and create new ones (full replacement strategy)
    $this->repository->deleteAllVariants($product);  // ← DELETES ALL!

    foreach ($dto->variants as $variantData) {
        $variant = $this->repository->createVariant($product, [...]);
    }
}
```

**Problem:**
- **EXTREMELY DANGEROUS:** Deletes ALL variants and recreates them
- Breaks cart item references (cart items reference `product_variant_id`)
- Breaks order item references (order items reference `product_variant_id`)
- Breaks review references
- Breaks variant image relations
- Breaks default variant references (`product_variant_id`)

**Required Fix:**
Implement SMART VARIANT SYNC:
- Existing variants update in-place (preserve ID)
- New variants create
- Missing variants soft-delete (don't hard delete!)
- Preserve IDs
- Preserve images
- Preserve default variant if still valid

**Backward Compatibility:**
- Critical for production systems with active carts/orders
- Must preserve referential integrity

---

### Issue #4: Default Variant Selection Logic is WRONG 🟠 HIGH

**Location:** `app/Http/Resources/Admin/Product/AdminProductDetailResource.php` (lines 15-21)

**Current State:**
```php
$defaultVariant = $this->relationLoaded('variants')
    ? ($this->variants
        ->where('is_active', true)
        ->sortBy('price')  // ← WRONG: picks cheapest!
        ->first()
        ?? $this->variants->first())
    : null;
```

**Problem:**
- Selects cheapest variant instead of designated default
- Should use `products.product_variant_id` field
- Inconsistent behavior across resources

**Required Fix:**
```php
$defaultVariant = $this->whenLoaded('defaultVariant', function () {
    return $this->defaultVariant;
}) ?? $this->primaryVariant();
```

**Model Already Has Correct Logic:**
```php
// app/Models/Product.php (lines 54-69)
public function primaryVariant(): ?ProductVariant
{
    if ($this->product_variant_id && $this->defaultVariant) {
        return $this->defaultVariant;
    }
    // fallback logic...
}
```

**Backward Compatibility:**
- Frontend may rely on current behavior
- Should be safe as `primaryVariant()` has fallback logic

---

### Issue #5: Resource References Non-Existent Columns 🟠 HIGH

**Location:** `app/Http/Resources/Admin/Product/AdminProductDetailResource.php` (lines 42-52)

**Current State:**
```php
'cost_per_item' => $defaultVariant?->cost_per_item ?? null,  // ← WRONG column name
'barcode'       => $defaultVariant?->barcode ?? null,        // ← Column doesn't exist
'weight'        => $defaultVariant?->weight ?? null,         // ← Column doesn't exist
'weight_unit'   => $defaultVariant?->weight_unit ?? null,    // ← Column doesn't exist
```

**Problem:**
- `cost_per_item` column doesn't exist → correct name is `cost_price`
- `barcode` column doesn't exist in `product_variants`
- `weight` column doesn't exist in `product_variants`
- `weight_unit` column doesn't exist in `product_variants`
- Causes runtime errors when these fields are accessed

**Database Schema (Actual):**
```php
// From migration: 2025_10_09_140107_create_product_variants_table.php
$table->decimal('cost_price', 12, 2)->nullable();
// NO barcode, weight, weight_unit columns
```

**Required Fix:**
Option A - Fix field names only:
```php
'cost_per_item' => $defaultVariant?->cost_price ?? null,
'barcode'       => null,  // or remove field
'weight'        => null,  // or remove field
'weight_unit'   => null,  // or remove field
```

Option B - Add missing columns via migration (recommended):
```php
Schema::table('product_variants', function (Blueprint $table) {
    $table->string('barcode')->nullable()->after('sku');
    $table->decimal('weight', 10, 2)->nullable()->after('track_inventory');
    $table->string('weight_unit')->nullable()->after('weight');
});
```

**Backward Compatibility:**
- Adding columns is safe (nullable)
- Removing fields may break frontend expectations

---

### Issue #6: findBySlugOrFail Implementation is BROKEN 🟠 HIGH

**Location:** `app/Models/Product.php` (lines 173-183)

**Current State:**
```php
public static function findBySlugOrFail(string $slug, ?string $locale = null): self
{
    // Use the scope properly via query builder
    $product = static::query()->findBySlug($slug, $locale)->first();
    
    if (!$product) {
        abort(404, 'Product not found.');
    }
    
    return $product;
}
```

**Problem:**
- Method signature suggests static call: `Product::findBySlugOrFail($slug)`
- But internally uses `static::query()->findBySlug()` which requires store scoping FIRST
- Current implementation IGNORES store context entirely
- Can return products from ANY store (cross-tenant leakage!)

**Architecture Rule Violation:**
From ARCHITECTURE.md:
> **HARD RULE — Store Scoping (CRITICAL)**
> ALL queries MUST be scoped by `store_id`.

**Required Fix:**
```php
/**
 * Find by slug or fail with 404.
 * 
 * IMPORTANT: This method MUST be called with store scoping.
 * Correct usage: Product::where('store_id', $storeId)->findBySlugOrFail($slug, $locale)
 */
public function scopeFindBySlugOrFail(Builder $query, string $slug, ?string $locale = null): self
{
    $product = $query->findBySlug($slug, $locale)->first();
    
    if (!$product) {
        abort(404, 'Product not found.');
    }
    
    return $product;
}
```

**Usage:**
```php
// CORRECT
Product::where('store_id', $storeId)->findBySlugOrFail($slug, $locale);

// WRONG - will fail or return wrong store's product
Product::findBySlugOrFail($slug, $locale);
```

**Backward Compatibility:**
- Requires updating all call sites to include store scoping
- May need temporary wrapper for legacy code

---

### Issue #7: Scope Grouping Bug Exists 🟠 MEDIUM

**Location:** `app/Models/Product.php` (lines 152-165)

**Current State:**
```php
public function scopeFindBySlug(Builder $query, string $slug, ?string $locale = null): Builder
{
    $locale = $locale ?? app()->getLocale();

    // Proper grouping: store scope AND (localized match OR fallback)
    return $query->whereHas('translations', function ($t) use ($slug, $locale) {
        // Try exact locale match first
        $t->where('slug', $slug)
          ->where(function ($q) use ($locale) {
              $q->where('locale', $locale);
          });
    });
}
```

**Problem:**
- Comment claims "proper grouping" but implementation is incomplete
- No fallback to other locales if exact match not found
- Relies on caller to have applied `where('store_id', $storeId)` first
- If caller forgets store scoping, cross-tenant leakage occurs

**Architecture Context:**
From prompt:
> Current slug scope logic has:
> `whereHas(...)->orWhereHas(...)`
> This risks: `(store scope AND localized match) OR (any locale any store)`

**Current Implementation Analysis:**
The current code is actually safer than described in the prompt (no `orWhereHas`), but still has issues:
1. No fallback locale logic
2. Depends entirely on caller for store scoping

**Recommended Enhancement:**
```php
public function scopeFindBySlug(Builder $query, string $slug, ?string $locale = null): Builder
{
    $locale = $locale ?? app()->getLocale();
    
    return $query->whereHas('translations', function ($t) use ($slug, $locale) {
        $t->where('slug', $slug)
          ->where(function ($q) use ($locale) {
              // Priority 1: Exact locale match
              $q->where('locale', $locale)
                // Priority 2: Fallback to first available locale
                ->orWhere(function ($fq) use ($slug) {
                    $fq->where('slug', $slug)
                       ->whereIn('locale', config('app.fallback_locale', ['en']));
                });
          });
    });
}
```

**Backward Compatibility:**
- Safe change, improves correctness

---

### Issue #8: Factories are OUTDATED 🟠 MEDIUM

**Location:** `database/factories/ProductFactory.php`

**Current State:**
```php
public function definition()
{
    $name = fake()->unique()->words(3, true);

    return [
        'name' => $name,                    // ← WRONG: should be in translations
        'slug' => Str::slug($name),         // ← WRONG: should be in translations
        'description' => fake()->sentence(12), // ← WRONG: should be in translations
        'category_id' => Category::inRandomOrder()->first()->id,
        'brand_id' => Brand::inRandomOrder()->first()->id,
        'is_active' => true,
    ];
}
```

**Problem:**
- Factory assumes `products` table owns `name`, `slug`, `description`
- These fields are OBSOLETE (moved to `product_translations`)
- Factory creates invalid product records
- Missing `store_id` (required field)
- Missing variant creation (products MUST have at least one variant)

**Required Fix:**
```php
public function definition()
{
    return [
        'store_id' => Store::factory(),
        'category_id' => Category::factory(),
        'brand_id' => Brand::factory(),
        'product_variant_id' => null, // Will be set after variant creation
        'is_active' => true,
    ];
}

public function configure()
{
    return $this->afterCreating(function (Product $product) {
        // Create translations
        $name = fake()->unique()->words(3, true);
        $product->translations()->create([
            'locale' => 'en',
            'name' => $name,
            'description' => fake()->sentence(12),
            'slug' => Str::slug($name),
        ]);
        
        // Create default variant (REQUIRED)
        $variant = $product->variants()->create([
            'sku' => strtoupper(Str::random(10)),
            'price' => fake()->randomFloat(2, 20, 200),
            'quantity' => fake()->numberBetween(10, 100),
            'is_active' => true,
        ]);
        
        // Set default variant
        $product->update(['product_variant_id' => $variant->id]);
    });
}
```

**Backward Compatibility:**
- Only affects test/seeder data generation
- Safe to update

---

### Issue #9: ProductVariantFactory Missing store_id 🟡 LOW

**Location:** `database/factories/ProductVariantFactory.php`

**Current State:**
```php
public function definition(): array
{
    return [
        'product_id' => $this->faker->randomElement(Product::pluck('id')->toArray()),
        'sku' => strtoupper(fake()->unique()->bothify('SKU-???-###')),
        // ...
    ];
}
```

**Problem:**
- Missing `store_id` field (will be added via migration)
- SKU generation doesn't account for store scoping
- Random product selection may cross store boundaries

**Required Fix:**
```php
public function definition(): array
{
    $product = Product::inRandomOrder()->first();
    
    return [
        'product_id' => $product->id,
        'store_id' => $product->store_id, // ← ADD THIS
        'sku' => strtoupper(Str::random(10)), // Use non-unique for multi-store
        // ...
    ];
}
```

**Backward Compatibility:**
- Only affects test/seeder data

---

### Issue #10: ProductSeeder Needs Review 🟡 LOW

**Location:** `database/seeders/ProductSeeder.php`

**Current State:**
The seeder correctly:
- Creates products with `store_id`
- Creates translations for multiple locales
- Creates variants with attributes
- Sets default variant

**Observations:**
- Uses `Store::first()->id` - hardcoded to first store
- Could be improved to support multiple stores
- Otherwise follows variant-first architecture correctly

**Recommendation:**
No immediate fix required. Consider enhancement for multi-store seeding.

---

### Issue #11: Description Column Type is WRONG 🟡 LOW

**Location:** `database/migrations/2025_10_09_140030_create_product_translations_table.php`

**Current State:**
```php
$table->string('description');
```

**Problem:**
- `string()` creates VARCHAR(255) - too short for product descriptions
- Should be `text()` for longer content
- Should be `nullable()` per architecture

**Required Fix:**
```php
$table->text('description')->nullable();
```

**Migration:**
```php
Schema::table('product_translations', function (Blueprint $table) {
    $table->text('description')->nullable()->change();
});
```

**Backward Compatibility:**
- Safe change (widening column type)
- Existing short descriptions remain valid

---

### Issue #12: Missing Model Casts 🟡 LOW

**Location:** `app/Models/ProductVariant.php`

**Current State:**
```php
public function casts(): array
{
    return [
        'price' => 'float',
    ];
}
```

**Missing Casts:**
```php
return [
    'price' => 'decimal:2',
    'compare_at_price' => 'decimal:2',
    'cost_price' => 'decimal:2',
    'quantity' => 'integer',
    'low_stock_threshold' => 'integer',
    'track_inventory' => 'boolean',
    'is_active' => 'boolean',
    'manufacture_date' => 'date',
    'expiry_date' => 'date',
];
```

**Backward Compatibility:**
- Safe addition
- Improves data consistency

---

### Issue #13: Products Table Still Has Legacy sku Column 🟡 TECHNICAL DEBT

**Location:** `database/migrations/2025_10_09_140029_create_products_table.php`

**Current State:**
```php
$table->string('sku')->unique()->nullable();
```

**Problem:**
- Legacy field from old architecture
- SKU now belongs to `product_variants` only
- Confusing for developers
- Violates variant-first architecture

**Architecture Rule:**
> IMPORTANT: The products table STILL has an old sku column accidentally left from legacy architecture.
> This is technical debt and must eventually be removed.

**Migration Plan (PHASE 2):**
1. Ensure no code references `products.sku`
2. Create migration to drop column
3. Update any raw SQL queries

**Current Usage Check:**
- Product model does NOT include `sku` in `$fillable`
- Product model provides accessor: `getSkuAttribute()` → delegates to primary variant
- Safe to remove once verified no direct DB queries use it

**Backward Compatibility:**
- Must ensure all code uses variant-level SKU
- May need deprecation period

---

## Fixed Issues ✅

### Issue #14: Attribute Relationship Typo - FIXED

**Location:** `app/Models/Attribute.php`

**Previous State:**
```php
public function variatns()  // ← TYPO
{
    return $this->belongsToMany(ProductVariant::class, 'variant_id'); // ← INCOMPLETE
}
```

**Current State (Already Fixed):**
```php
public function variants()
{
    return $this->belongsToMany(ProductVariant::class, 'variant_attribute_values', 'attribute_id', 'variant_id');
}
```

**Status:** ✅ RESOLVED

---

### Issue #15: VariantAttributeValue Relationship - VERIFIED CORRECT

**Location:** `app/Models/VariantAttributeValue.php`

**Prompt Claim:**
> VariantAttributeValue incorrectly uses: belongsToMany()
> It should use: belongsTo()

**Actual State:**
```php
public function variant()
{
    return $this->belongsTo(ProductVariant::class, 'variant_id');
}

public function attribute()
{
    return $this->belongsTo(Attribute::class, 'attribute_id');
}

public function attributeValue()
{
    return $this->belongsTo(AttributeValue::class, 'attribute_value_id');
}
```

**Status:** ✅ ALREADY CORRECT - Prompt was outdated

---

## Schema Comparison: Current vs Target

### product_translations Table

| Field | Current | Target | Status |
|-------|---------|--------|--------|
| id | ✅ | ✅ | OK |
| product_id | ✅ | ✅ | OK |
| store_id | ❌ MISSING | ✅ REQUIRED | 🔴 NEEDS MIGRATION |
| locale | ✅ | ✅ | OK |
| name | ✅ | ✅ | OK |
| description | string | text NULLABLE | 🟡 NEEDS CHANGE |
| seo_title | ✅ (added later) | ✅ | OK |
| seo_description | ✅ (added later) | ✅ | OK |
| slug | ✅ | ✅ | OK |
| UNIQUE | (slug, locale) | (slug, locale, store_id) | 🔴 NEEDS MIGRATION |

### product_variants Table

| Field | Current | Target | Status |
|-------|---------|--------|--------|
| id | ✅ | ✅ | OK |
| product_id | ✅ | ✅ | OK |
| store_id | ❌ MISSING | ✅ REQUIRED | 🔴 NEEDS MIGRATION |
| sku | unique | unique(store_id, sku) | 🔴 NEEDS MIGRATION |
| price | ✅ | ✅ | OK |
| compare_at_price | ✅ | ✅ | OK |
| cost_price | ✅ | ✅ | OK |
| quantity | ✅ | ✅ | OK |
| low_stock_threshold | ✅ | ✅ | OK |
| track_inventory | ✅ | ✅ | OK |
| barcode | ❌ MISSING | ✅ NULLABLE | 🟡 OPTIONAL |
| weight | ❌ MISSING | ✅ NULLABLE | 🟡 OPTIONAL |
| weight_unit | ❌ MISSING | ✅ NULLABLE | 🟡 OPTIONAL |
| manufacture_date | ✅ | ✅ | OK |
| expiry_date | ✅ | ✅ | OK |
| batch_number | ✅ | ✅ | OK |
| position | ✅ | ✅ | OK |
| is_active | ✅ | ✅ | OK |

---

## Implementation Priority

### PHASE 1: SAFE IMMEDIATE FIXES (No Schema Changes)

Priority | Issue | Risk | Effort
--------|-------|------|-------
1 | #3: Destructive Product Update | 🔴 CRITICAL | Medium
2 | #4: Default Variant Logic | 🟠 HIGH | Low
3 | #5: Non-Existent Column References | 🟠 HIGH | Low
4 | #6: findBySlugOrFail Implementation | 🟠 HIGH | Low
5 | #8: Factory Updates | 🟠 MEDIUM | Medium
6 | #12: Missing Model Casts | 🟡 LOW | Low

### PHASE 2: SCHEMA FIXES (Requires Migrations)

Priority | Issue | Risk | Effort
--------|-------|------|-------
1 | #1: Slug Uniqueness (add store_id) | 🔴 CRITICAL | High
2 | #2: SKU Uniqueness (add store_id) | 🔴 CRITICAL | High
3 | #11: Description Column Type | 🟡 LOW | Low
4 | #5B: Add Missing Columns (barcode, weight) | 🟡 LOW | Low
5 | #13: Remove products.sku | 🟡 TECH DEBT | Medium

### PHASE 3: ARCHITECTURE ALIGNMENT

Priority | Issue | Risk | Effort
--------|-------|------|-------
1 | #7: Scope Grouping Enhancement | 🟠 MEDIUM | Low
2 | Smart Variant Sync Implementation | 🔴 CRITICAL | High
3 | Display Variant Response Structure | 🟠 MEDIUM | Medium
4 | ProductService Anti-Pattern Removal | 🟡 LOW | Medium

---

## Backward Compatibility Strategy

### API Response Changes

**Current (Flattened - WRONG):**
```json
{
  "id": 1,
  "sku": "TSHIRT-RED-L",
  "price": 29.99,
  "quantity": 50
}
```

**Target (Nested - CORRECT):**
```json
{
  "id": 1,
  "display_variant": {
    "id": 101,
    "sku": "TSHIRT-RED-L",
    "price": 29.99
  },
  "variants": [...]
}
```

**Migration Strategy:**
1. Add `display_variant` field alongside flattened fields
2. Mark flattened fields as `@deprecated` in API docs
3. Maintain both for 2 release cycles
4. Remove flattened fields in major version

### Database Migration Safety

**Golden Rules:**
1. NEVER drop columns in same migration as adding replacements
2. ALWAYS backfill data before adding constraints
3. Use nullable columns initially, add validation later
4. Test migrations on production-like data volume

**Example Migration Sequence:**

```php
// Migration 1: Add store_id (nullable)
Schema::table('product_variants', function (Blueprint $table) {
    $table->foreignId('store_id')->nullable()->constrained('stores');
});

// Migration 2: Backfill store_id
DB::statement('UPDATE product_variants pv 
               JOIN products p ON pv.product_id = p.id 
               SET pv.store_id = p.store_id');

// Migration 3: Make store_id required
Schema::table('product_variants', function (Blueprint $table) {
    $table->foreignId('store_id')->nullable(false)->change();
});

// Migration 4: Drop old unique, add new
Schema::table('product_variants', function (Blueprint $table) {
    $table->dropUnique(['sku']);
});

Schema::table('product_variants', function (Blueprint $table) {
    $table->unique(['sku', 'store_id']);
});
```

---

## Testing Requirements

### Regression Tests Required

1. **Cross-Tenant Slug Leakage Test**
   ```php
   // Create same slug in different stores
   Product in Store A with slug 'iphone-14'
   Product in Store B with slug 'iphone-14'
   
   // Query with store context
   Product::where('store_id', StoreA)->findBySlug('iphone-14') // Should find Store A's product
   Product::where('store_id', StoreB)->findBySlug('iphone-14') // Should find Store B's product
   ```

2. **Cart Reference Preservation Test**
   ```php
   // Add item to cart
   $cartItem = CartItem::create(['product_variant_id' => $variant->id]);
   
   // Update product (should NOT delete variant)
   UpdateProductAction->execute($dto);
   
   // Cart item should still reference valid variant
   $cartItem->fresh()->product_variant_id === $variant->id // Should be true
   ```

3. **Default Variant Persistence Test**
   ```php
   // Set default variant
   $product->update(['product_variant_id' => $variant->id]);
   
   // Update product without changing variants
   UpdateProductAction->execute($dtoWithoutVariants);
   
   // Default variant should be preserved
   $product->fresh()->product_variant_id === $variant->id // Should be true
   ```

---

## Recommendations

### Immediate Actions (This Sprint)

1. ✅ Fix destructive UpdateProductAction (Issue #3)
2. ✅ Fix AdminProductDetailResource column references (Issue #5)
3. ✅ Fix default variant selection (Issue #4)
4. ✅ Add proper model casts (Issue #12)

### Next Sprint

1. Create and run schema migrations for store_id (Issues #1, #2)
2. Update factories and seeders (Issues #8, #9)
3. Add regression tests for all critical paths

### Future Sprints

1. Remove legacy `products.sku` column (Issue #13)
2. Implement display_variant response structure
3. Clean up deprecated flattened fields

---

## Conclusion

The codebase is mostly aligned with the variant-first architecture but has several critical issues that must be addressed:

1. **Store scoping for slugs and SKUs** is the highest priority for multi-tenant integrity
2. **Destructive variant updates** pose immediate risk to data integrity
3. **Incorrect column references** cause runtime errors

All fixes should follow the phased approach to maintain backward compatibility and prevent production incidents.

---

**Report Generated:** 2025-05-10  
**Next Review:** After PHASE 1 completion
