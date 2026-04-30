# Admin API Documentation

## Phase 3.1 — Admin Users API

### Endpoints

| Method | URL | Permission | Description |
|--------|-----|------------|-------------|
| GET    | /api/v1/admin/stores/{store}/users | user.view | List all users in store |
| GET    | /api/v1/admin/stores/{store}/users/{user} | user.view | Get single user detail |
| PATCH  | /api/v1/admin/stores/{store}/users/{user}/block | user.block | Block a user |
| PATCH  | /api/v1/admin/stores/{store}/users/{user}/unblock | user.block | Unblock a user |
| DELETE | /api/v1/admin/stores/{store}/users/{user} | user.delete | Soft delete a user |
| PATCH  | /api/v1/admin/stores/{store}/users/{user}/restore | user.restore | Restore a soft-deleted user |

---

### Files Created

#### Controller
- `app/Http/Controllers/Api/Admin/User/AdminUserController.php`

#### DTOs
- `app/DTOs/Admin/User/ListUsersDTO.php`
- `app/DTOs/Admin/User/GetUserDTO.php`
- `app/DTOs/Admin/User/BlockUserDTO.php`
- `app/DTOs/Admin/User/UnblockUserDTO.php`
- `app/DTOs/Admin/User/DeleteUserDTO.php`
- `app/DTOs/Admin/User/RestoreUserDTO.php`

#### Actions
- `app/Actions/Admin/User/ListUsersAction.php`
- `app/Actions/Admin/User/GetUserAction.php`
- `app/Actions/Admin/User/BlockUserAction.php`
- `app/Actions/Admin/User/UnblockUserAction.php`
- `app/Actions/Admin/User/DeleteUserAction.php`
- `app/Actions/Admin/User/RestoreUserAction.php`

#### Repository
- `app/Repositories/Admin/User/AdminUserRepository.php`

#### Requests
- `app/Http/Requests/Admin/User/ListUsersRequest.php`

#### Resources
- `app/Http/Resources/Admin/User/AdminUserResource.php`
- `app/Http/Resources/Admin/User/AdminUserDetailResource.php`

#### Exceptions
- `app/Exceptions/User/UserNotFoundException.php`

---

### ErrorCodes Added

| Code | Value | Meaning |
|------|-------|---------|
| USR_001 | `'USR_001'` | User not found |

Added to: `app/Enums/ErrorCode.php`

---

### Localization Keys Added

#### `lang/en/error.php`
- `'user_not_found' => 'User not found.'`

#### `lang/ar/error.php`
- `'user_not_found' => 'المستخدم غير موجود.'`

#### `lang/en/admin.php`
- `'user_blocked'   => 'User has been blocked.'`
- `'user_unblocked' => 'User has been unblocked.'`
- `'user_deleted'   => 'User has been deleted.'`
- `'user_restored'  => 'User has been restored.'`

#### `lang/ar/admin.php`
- `'user_blocked'   => 'تم حظر المستخدم.'`
- `'user_unblocked' => 'تم رفع الحظر عن المستخدم.'`
- `'user_deleted'   => 'تم حذف المستخدم.'`
- `'user_restored'  => 'تم استعادة المستخدم.'`

---

### Middleware Stack

All Admin Users routes use:

```
auth:sanctum → store.context → permission:{permission}
```

---

### Super Admin Bypass

In every Action, before checking store membership:

```php
if (!$request->user()->hasRole(RoleEnum::SUPER_ADMIN)) {
    if (!$user->stores()->where('store_id', $dto->storeId)->exists()) {
        throw new UnauthorizedStoreAccessException();
    }
}
```

`super_admin` bypasses the store membership check in ALL Admin User actions.

---

### Response Format

#### List Users
```json
{
  "status": true,
  "message": "Success",
  "data": [ ...AdminUserResource ],
  "meta": { ...pagination }
}
```

#### Single User
```json
{
  "status": true,
  "message": "Success",
  "data": { ...AdminUserDetailResource }
}
```

#### Block / Unblock / Restore
```json
{
  "status": true,
  "message": "User has been blocked.",
  "data": { ...AdminUserResource }
}
```

#### Delete
```json
{
  "status": true,
  "message": "User has been deleted.",
  "data": null
}
```

---

### Store Scoping

Users are scoped to a store via the `store_user` pivot table.

All repository queries check:
```php
->whereHas('stores', fn($q) => $q->where('store_id', $storeId))
```

Restore queries use `withTrashed()` and scope by `store_id`.

---

### Architecture Compliance

- [x] storeId is first param in every DTO
- [x] storeId comes from route param only
- [x] No DB queries outside AdminUserRepository
- [x] No business logic in controller
- [x] No try/catch in controller or actions
- [x] No hardcoded strings — PermissionEnum + __() used
- [x] No response()->json() — ApiResponserTrait used
- [x] All queries scoped by store_id
- [x] super_admin bypasses store membership check
- [x] Admin resources domain-grouped under Resources/Admin/User/

---

## Phase 3.2 — Admin Products API

### Endpoints

| Method | URL | Permission | Description |
|--------|-----|------------|-------------|
| GET    | /api/v1/admin/stores/{store}/products | product.view | List store products paginated |
| GET    | /api/v1/admin/stores/{store}/products/{product} | product.view | Get single product with variants |
| POST   | /api/v1/admin/stores/{store}/products | product.create | Create product with variants |
| PATCH  | /api/v1/admin/stores/{store}/products/{product} | product.update | Update product details |
| DELETE | /api/v1/admin/stores/{store}/products/{product} | product.delete | Soft delete product |
| PATCH  | /api/v1/admin/stores/{store}/products/{product}/restore | product.restore | Restore soft-deleted product |

---

### Files Created

#### Controller
- `app/Http/Controllers/Api/Admin/Product/AdminProductController.php`

#### DTOs
- `app/DTOs/Admin/Product/ListProductsDTO.php`
- `app/DTOs/Admin/Product/GetProductDTO.php`
- `app/DTOs/Admin/Product/CreateProductDTO.php`
- `app/DTOs/Admin/Product/UpdateProductDTO.php`
- `app/DTOs/Admin/Product/DeleteProductDTO.php`
- `app/DTOs/Admin/Product/RestoreProductDTO.php`

#### Actions
- `app/Actions/Admin/Product/ListProductsAction.php`
- `app/Actions/Admin/Product/GetProductAction.php`
- `app/Actions/Admin/Product/CreateProductAction.php`
- `app/Actions/Admin/Product/UpdateProductAction.php`
- `app/Actions/Admin/Product/DeleteProductAction.php`
- `app/Actions/Admin/Product/RestoreProductAction.php`

#### Repository
- `app/Repositories/Admin/Product/AdminProductRepository.php`

#### Requests
- `app/Http/Requests/Admin/Product/ListProductsRequest.php`
- `app/Http/Requests/Admin/Product/GetProductRequest.php`
- `app/Http/Requests/Admin/Product/CreateProductRequest.php`
- `app/Http/Requests/Admin/Product/UpdateProductRequest.php`
- `app/Http/Requests/Admin/Product/DeleteProductRequest.php`
- `app/Http/Requests/Admin/Product/RestoreProductRequest.php`

#### Resources
- `app/Http/Resources/Admin/Product/AdminProductResource.php`
- `app/Http/Resources/Admin/Product/AdminProductDetailResource.php`

#### Exceptions
- `app/Exceptions/Product/ProductNotFoundException.php` (created if not existed)

---

### ErrorCodes Added

| Code | Value | Meaning |
|------|-------|---------|
| PRD_002 | `'PRD_002'` | Product not found in store |
| PRD_003 | `'PRD_003'` | Product restore failed |

Added to: `app/Enums/ErrorCode.php`

---

### Localization Keys Added

#### `lang/en/admin.php`
- `'product_created'      => 'Product created successfully.'`
- `'product_updated'      => 'Product updated successfully.'`
- `'product_deleted'      => 'Product deleted successfully.'`
- `'product_restored'     => 'Product restored successfully.'`
- `'variant_required'     => 'At least one variant is required.'`
- `'media_upload_failed'  => 'Media upload failed.'`

#### `lang/ar/admin.php`
- `'product_created'      => 'تم إنشاء المنتج بنجاح.'`
- `'product_updated'      => 'تم تحديث المنتج بنجاح.'`
- `'product_deleted'      => 'تم حذف المنتج بنجاح.'`
- `'product_restored'     => 'تم استعادة المنتج بنجاح.'`
- `'variant_required'     => 'مطلوب متغير واحد على الأقل.'`
- `'media_upload_failed'  => 'فشل رفع الوسائط.'`

---

### Middleware Stack

All Admin Products routes use:

```
auth:sanctum → store.context → permission:{permission}
```

---

### Super Admin Bypass

In every Action, before executing business logic:

```php
if (!auth()->user()->hasRole(RoleEnum::SUPER_ADMIN)) {
    if (!auth()->user()->stores()->where('store_id', $dto->storeId)->exists()) {
        throw new UnauthorizedStoreAccessException();
    }
}
```

`super_admin` bypasses store membership check in ALL Admin Product actions.

---

### Store Scoping

All repository queries are scoped by `store_id`:

```php
Product::where('store_id', $storeId)->paginate();
Product::where('store_id', $storeId)->findOrFail($productId);
Product::withTrashed()->where('store_id', $storeId)->findOrFail($productId)->restore();
```

Variants share the same `store_id` as their parent product.
Create operations use `DB::transaction()` to ensure atomicity across product + variants + media.

---

### Response Format

#### List Products
```json
{
  "status": true,
  "message": "Success",
  "data": [ ...AdminProductResource ],
  "meta": { ...pagination }
}
```

#### Single Product
```json
{
  "status": true,
  "message": "Success",
  "data": { ...AdminProductDetailResource }
}
```

#### Create / Update / Restore
```json
{
  "status": true,
  "message": "Product created successfully.",
  "data": { ...AdminProductDetailResource }
}
```

#### Delete
```json
{
  "status": true,
  "message": "Product deleted successfully.",
  "data": null
}
```

---

### Architecture Compliance

- [x] storeId is first param in every DTO
- [x] storeId comes from route param only
- [x] No DB queries outside AdminProductRepository
- [x] No business logic in controller
- [x] No try/catch in controller or actions
- [x] No hardcoded strings — PermissionEnum + __() used
- [x] No response()->json() — ApiResponserTrait used
- [x] All queries scoped by store_id
- [x] super_admin bypasses store membership check
- [x] Admin resources domain-grouped under Resources/Admin/Product/
- [x] Create uses DB::transaction() for atomicity
