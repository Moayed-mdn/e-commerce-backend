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
