# Laravel API Architecture Rules (Project Contract)

This document defines the **mandatory architecture** for this project.
All contributors (human or AI) MUST follow these rules strictly.

---

# 1. Core Philosophy

This project follows a strict API-first architecture with clear separation of concerns.

### Rules:

* The application is a **pure REST API** (no Blade, no mixed rendering logic)
* Controllers must remain **thin and declarative**
* Business logic MUST NOT exist in Controllers or Models
* Code must be **predictable, testable, and scalable**
* Use **strict typing (PHP 8+) everywhere**
* All responses must follow a **unified API format**

### Goal:

Maintain a clean, scalable, and consistent codebase across teams and AI tools.

---

# 2. Project Structure (Domain-Driven)

Every layer must be grouped by **domain (feature)** before **type**. This is a core principle of this architecture.

### Correct Structure

```plaintext
app/
 ├── Actions/
 │    ├── Cart/
 │    ├── Auth/
 │    ├── Order/
 │    ├── Product/
 │    ├── Payment/
 │    ├── (a Domain ....)
 │
 ├── DTOs/
 │    ├── Cart/
 │    ├── Auth/
 │    ├── Order/
 │    ├── Product/
 │    ├── Payment/
 │    ├── (a Domain ....)
 │
 ├── Repositories/
 │    ├── Cart/
 │    ├── Order/
 │    ├── Product/
 │    ├── (a Domain ....)
 │
 ├── Services/
 │    ├── Payment/
 │    ├── (a Domain ....)
 │
 ├── Http/
 │    ├── Controllers/
 │    │    ├── Cart/
 │    │    ├── Auth/
 │    │    ├── Order/
 │    │    ├── Product/
 │    │    ├── Payment/
 │    │    ├── (a Domain ....)
 │
 │    ├── Requests/
 │    │    ├── Cart/
 │    │    ├── Auth/
 │    │    ├── Order/
 │    │    ├── Product/
 │    │    ├── Payment/
 │    │    ├── (a Domain ....)
 │
 │    ├── Resources/
 │    │    ├── Cart/
 │    │    ├── Order/
 │    │    ├── Product/
 │    │    ├── (a Domain ....)
```

### Core Rules

#### 1. Domain First
- Every file MUST belong to a domain.
- Domains reflect business capabilities, not technical types.
- **Examples**: `Cart`, `Auth`, `Order`, `Product`, `Payment`.

#### 2. No Flat Structures
- **Forbidden**:
  ```plaintext
  Actions/
   ├── AddToCartAction.php
   ├── LoginUserAction.php
   ├── CreateOrderAction.php
  ```
- **Required**:
  ```plaintext
  Actions/
   ├── Cart/AddToCartAction.php
   ├── Auth/LoginUserAction.php
   ├── Order/CreateOrderAction.php
  ```

#### 3. Cross-Layer Consistency
- Each use-case MUST stay within the same domain across all layers.
- **Example (`Cart` use case):**
  ```plaintext
  Http/Requests/Cart/AddToCartRequest.php
  DTOs/Cart/AddToCartDTO.php
  Actions/Cart/AddToCartAction.php
  Repositories/Cart/CartRepository.php
  Http/Resources/Cart/CartResource.php
  ```

#### 4. No Cross-Domain Leakage
- `Cart` MUST NOT contain `Order` logic.
- `Auth` MUST NOT contain `Payment` logic.
- If interaction is needed → use **Services**.

#### 5. Services as Cross-Domain Orchestrators
- Services may coordinate multiple domains.
- **Example**: `Services/Payment/CheckoutService.php` can orchestrate a flow like: `Cart` → `Order` → `Payment`.

---

# 3. Controllers

Controllers are **entry points only**.

### Responsibilities:

* Accept request
* Call Action/Service
* Return response

### Rules:

* MUST be thin (≈10–15 lines)
* MUST NOT contain business logic
* MUST NOT access Models directly
* MUST NOT perform validation
* MUST NOT handle exceptions manually
* MUST return responses via `ApiResponserTrait`

---

# 4. Business Logic (Actions & Services)

## Actions

Single-responsibility operations.

### Rules:

* One responsibility only
* Accept DTO
* Return Model or Value Object

## Services

Complex workflows.

### Rules:

* Orchestrate multiple Actions
* No request handling
* Keep logic readable and maintainable

---

# 5. DTOs (Data Transfer Objects)

DTOs are **mandatory**.

### Rules:

* Every Action MUST receive a DTO
* DTOs must be strictly typed
* DTOs must be immutable
* No arrays in business logic
* Provide `fromRequest()` factory

---

# 6. Repositories

Repositories are the **only DB access layer**.

### Rules:

* No DB queries outside repositories
* No business logic inside repositories
* Return Models or Collections only

---

# 7. API Responses

All responses are **centralized and standardized**.

---

## Response System

Uses:

* `ApiResponserTrait`

```php
abstract class Controller
{
    use ApiResponserTrait;
}
```

---

## Response Format

### Success

```json
{
  "status": true,
  "message": "Success",
  "data": {}
}
```

### Error

```json
{
  "status": false,
  "message": "Error message",
  "error_code": "ERROR_CODE",
  "errors": {}
}
```

---

## Rules

* Controllers MUST use:

  * `$this->success()`
  * `$this->paginated()`
* API Resources are **mandatory**
* Trait handles structure, Resources handle transformation

---

## Examples

```php
return $this->success(
    new CartResource($cart)
);
```

```php
return $this->paginated(
    CartResource::collection($carts)
);
```

---

## Forbidden

* Returning `response()->json()` directly
* Returning raw Models or arrays
* Bypassing ApiResponserTrait

---

# 8. Error Handling

Error handling is **centralized and exception-driven**.

---

## ErrorCode Enum

```plaintext
app/Enums/ErrorCode.php
```

### Rules:

* ALL errors MUST use `ErrorCode`
* No hardcoded error codes
* Acts as contract with frontend

---

## Custom Exceptions

```plaintext
app/Exceptions/
 ├── BaseApiException.php
 ├── Auth/
 ├── Order/
 ├── Payment/
```

### Rules:

* Extend `BaseApiException`
* Define:

  * message
  * status code
  * error code

---

## Exception Registration

```php
->withExceptions(function (Exceptions $exceptions): void {
    app(ExceptionRegistrar::class)->handle($exceptions);
})
```

---

## Error Response Format

```json
{
  "status": false,
  "message": "Error message",
  "error_code": "ERROR_CODE",
  "errors": {}
}
```

---

## Handled Cases

### Business Exceptions

```php
if ($e instanceof BaseApiException) {
    return $e->render(request());
}
```

### Validation

```php
if ($e instanceof ValidationException) {
    return response()->json([
        'status' => false,
        'message' => 'Validation failed',
        'error_code' => ErrorCode::VAL_001->value,
        'errors' => $e->errors(),
    ], 422);
}
```

### HTTP

```php
if ($e instanceof HttpExceptionInterface) {
    return response()->json([
        'status' => false,
        'message' => $e->getMessage(),
        'error_code' => ErrorCode::HTTP_GENERIC->value,
        'errors' => null,
    ], $e->getStatusCode());
}
```

### System

```php
Log::error($e);

return response()->json([
    'status' => false,
    'message' => config('app.env') === 'local'
        ? $e->getMessage()
        : 'Server Error',
    'error_code' => ErrorCode::SYS_001->value,
    'errors' => null,
], 500);
```

---

## Rules

* No try/catch in Controllers
* No manual error responses
* No raw exceptions returned
* No sensitive data exposure
* No hardcoded error codes

---

## Example: OutOfStockException

```php
class OutOfStockException extends BaseApiException
{
    public function __construct()
    {
        parent::__construct(
            message: __('order.out_of_stock'),
            errorCode: ErrorCode::ORDER_OUT_OF_STOCK->value,
            statusCode: 400
        );
    }
}
```

Usage:

```php
if ($product->stock < $dto->quantity) {
    throw new OutOfStockException();
}
```

---

# 9. Validation

Handled via FormRequest only.

### Rules:

* No validation outside FormRequest
* Rules must be explicit and strict

---

# 10. Naming Conventions

* Actions → `Verb + Entity + Action`
* DTOs → `UseCase + DTO`
* Requests → `UseCaseRequest`
* Resources → `EntityResource`
* Repositories → `EntityRepository`

---

# 11. Anti-Patterns (Forbidden)

* Fat Controllers
* Business logic in Models
* Static helpers for logic
* Direct `request()` usage
* Raw arrays or Models in responses
* Layer mixing

---

# 12. Performance Rules

* Use eager loading
* Avoid N+1 queries
* Cache heavy data when needed

---

# 13. Flow (Golden Path)

```plaintext
Request
 → FormRequest
 → DTO
 → Action
 → Repository
 → Resource
 → ApiResponserTrait
```

### Rule:

No step may be skipped.

---

# 14. Real Example: Cart (Add to Cart)

## Route

```plaintext
POST /api/cart/items
```

---

## Form Request

```php
class AddToCartRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ];
    }
}
```

---

## DTO

```php
class AddToCartDTO
{
    public function __construct(
        public int $productId,
        public int $quantity,
        public int $userId,
    ) {}

    public static function fromRequest(AddToCartRequest $request): self
    {
        return new self(
            $request->integer('product_id'),
            $request->integer('quantity'),
            $request->user()->id,
        );
    }
}
```

---

## Repository

```php
class CartRepository
{
    public function getUserCart(int $userId): Cart
    {
        return Cart::firstOrCreate(['user_id' => $userId]);
    }

    public function addItem(Cart $cart, int $productId, int $quantity): CartItem
    {
        return $cart->items()->updateOrCreate(
            ['product_id' => $productId],
            ['quantity' => DB::raw("quantity + $quantity")]
        );
    }
}
```

---

## Action

```php
class AddToCartAction
{
    public function __construct(
        private CartRepository $cartRepository
    ) {}

    public function execute(AddToCartDTO $dto): Cart
    {
        $cart = $this->cartRepository->getUserCart($dto->userId);

        $this->cartRepository->addItem(
            $cart,
            $dto->productId,
            $dto->quantity
        );

        return $cart->load('items.product');
    }
}
```

---

## Controller

```php
class CartController extends Controller
{
    public function store(AddToCartRequest $request, AddToCartAction $action)
    {
        $cart = $action->execute(
            AddToCartDTO::fromRequest($request)
        );

        return $this->success(
            new CartResource($cart)
        );
    }
}
```

---

## Resource

```php
class CartResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'items' => CartItemResource::collection($this->items),
            'total' => $this->items->sum(
                fn($item) => $item->price * $item->quantity
            ),
        ];
    }
}
```

---

## Key Takeaways

* Thin Controller
* DTO enforced
* Business logic isolated
* Repository used
* Resource + Trait used
* Flow respected

---

# 15. Localization (Multilingual Support)

The project supports multiple languages:

* English (`en`)
* Arabic (`ar`)

---

## Language Structure

```plaintext
lang/
 ├── en/
 │    ├── auth.php
 │    ├── cart.php
 │    ├── order.php
 │    ├── payment.php
 │    ├── error.php
 │    ├── validation.php
 ├── ar/
 │    ├── auth.php
 │    ├── cart.php
 │    ├── order.php
 │    ├── payment.php
 │    ├── error.php
 │    ├── validation.php
```

---

## Rules

* All user-facing messages MUST use localization
* Use Laravel `__()` helper
* No hardcoded strings anywhere in the codebase

---

## Example

```php
__('order.out_of_stock')
```

---

## Middleware

Locale is resolved via middleware:

* Uses `Accept-Language` or `locale` header
* Falls back to supported locales
* Defaults to `config('app.locale')`

---

## Naming Convention

* Use domain-based keys:

```php
__('order.out_of_stock')
__('cart.item_added')
__('auth.invalid_credentials')
__('payment.failed')
```

---

## Goal

* Centralize all messages
* Enable multilingual support
* Keep consistency across API responses

---

# 16. Admin & Dashboard Architecture Rules

This section defines the Admin (Dashboard) system architecture, including:

*   Admin APIs
*   Role & permission system
*   Admin domains
*   Authorization strategy
*   Dashboard analytics

## 16.1 Core Principles

Rules:
*   Admin is NOT a separate model
*   Admin = User with role `admin` (via `spatie/laravel-permission`)
*   Admin APIs are strictly separated
*   Admin logic MUST NOT pollute user-facing domains
*   Admin follows the same architecture contract (DTO → Action → Repository → Resource)

## 16.2 API Structure

### Versioned Admin Routes

`/api/v1/admin/...`

### Examples:
```http
GET    /api/v1/admin/users
PATCH  /api/v1/admin/users/{id}/block
DELETE /api/v1/admin/users/{id}
POST   /api/v1/admin/products
PATCH  /api/v1/admin/orders/{id}/status
GET    /api/v1/admin/dashboard/stats
```

## 16.3 Folder Structure (Strict Separation)

Admin is treated as a top-level domain wrapper.

```plaintext
app/
 ├── Actions/
 │    ├── Admin/
 │    │    ├── User/
 │    │    ├── Product/
 │    │    ├── Order/
 │    │    ├── Dashboard/
 │
 ├── DTOs/
 │    ├── Admin/
 │    │    ├── User/
 │    │    ├── Product/
 │    │    ├── Order/
 │
 ├── Http/
 │    ├── Controllers/
 │    │    ├── Admin/
 │    │    │    ├── User/
 │    │    │    ├── Product/
 │    │    │    ├── Order/
 │    │    │    ├── Dashboard/
 │
 │    ├── Requests/
 │    │    ├── Admin/
 │    │    │    ├── User/
 │    │    │    ├── Product/
 │    │    │    ├── Order/
 │
 │    ├── Resources/
 │    │    ├── Admin/
 │    │    │    ├── User/
 │    │    │    ├── Product/
 │    │    │    ├── Order/
 │    │    │    ├── Dashboard/
```

## 16.4 Role & Permission System

Uses:

*   `spatie/laravel-permission`

### Roles
*   `admin`        → full access
*   `sub_admin`    → limited access
*   `customer`     → default users

### Permission Strategy (Granular)

Required Format:
`entity.action`

Examples:
*   `user.view`
*   `user.block`
*   `user.delete`
*   `user.restore`
*   `product.create`
*   `product.update`
*   `product.delete`
*   `order.view`
*   `order.update_status`
*   `order.cancel`
*   `order.refund`

### Rule: No Hardcoded Strings

Permissions MUST be defined as constants:

```php
class PermissionEnum
{
    public const USER_VIEW = 'user.view';
    public const USER_BLOCK = 'user.block';
}
```

## 16.5 Authorization Strategy (STRICT)

### 1. Middleware (Perimeter)

Used for route protection:

`->middleware('permission:user.view')`

### 2. Policies (Authorization ONLY)

Policies handle:

*   ✔ Who can perform the action
*   ❌ NO business logic

```php
public function update(User $user)
{
    return $user->hasPermissionTo('product.update');
}
```

### 3. Actions (Business Rules)

ALL domain rules go here:

```php
if ($product->is_locked) {
    throw new ProductLockedException();
}
```

### ❌ Forbidden
*   Business logic in Policies
*   Skipping middleware
*   Authorization inside Controllers

## 16.6 Admin Actions Rules

### Rule: Separate Admin Actions
`Actions/Admin/Product/CreateProductAction.php`

### Rule: No Logic Duplication

Admin Actions MAY reuse core Actions:

```php
class AdminCreateProductAction
{
    public function __construct(
        private CreateProductAction $createProduct
    ) {}

    public function execute(AdminCreateProductDTO $dto)
    {
        return $this->createProduct->execute(
            $dto->toBaseDTO()
        );
    }
}
```

## 16.7 DTO Rules (Admin)

### Rule: Separate DTOs
`DTOs/Admin/Product/CreateProductDTO.php`

### Rule: Strict Mapping

Admin DTOs MAY transform into core DTOs:

`public function toBaseDTO(): CreateProductDTO`

### ❌ Forbidden
*   Reusing user DTOs in admin
*   Passing Request directly to Actions

## 16.8 User Management Rules

### Customer Management (Admin)

Allowed:

*   ✔ View users
*   ✔ View details
*   ✔ Block / Unblock
*   ✔ Soft delete
*   ✔ Restore

👉 Yes — this is standard in e-commerce dashboards

### Sub-Admin Management

Allowed:

*   ✔ Create admins
*   ✔ Update admins
*   ✔ Assign roles
*   ✔ Block / Unblock
*   ✔ Delete / Restore

### Rule: Block Implementation
```php
$table->boolean('is_active')->default(true);
```

### Rule: Soft Deletes (MANDATORY)
```php
$table->softDeletes();
```

### Rule: Repository Scope
*   Default queries → only active users
*   Admin queries → may include trashed

## 16.9 Product Management (Complex)

Admin MUST support:

*   Variants (size, color)
*   Media
*   Categories
*   Pricing
*   Stock

### Rule

Complex operations MUST use:

*   Multiple Actions
*   OR a Service if orchestration is needed

## 16.10 Order Management

### Supported Operations

*   ✔ View orders
*   ✔ Change status
*   ✔ Cancel
*   ✔ Refund

### Status Enum Example
*   `pending`
*   `processing`
*   `shipped`
*   `delivered`
*   `cancelled`

### Rule

Each operation MUST be a separate Action:

*   `UpdateOrderStatusAction`
*   `CancelOrderAction`
*   `RefundOrderAction`

## 16.11 Dashboard Domain

### Location
`Actions/Admin/Dashboard/`

### Responsibilities
*   Aggregated data ONLY
*   No business mutations

### Example Actions
*   `GetDashboardStatsAction`
*   `GetRevenueStatsAction`
*   `GetOrdersStatsAction`

### Rule

Dashboard MUST NOT access Models directly
→ Use Repositories

## 16.12 Soft Delete Strategy

### Rules
*   All admin-managed entities SHOULD support soft deletes
*   Admin can:
    *   View trashed
    *   Restore
    *   Force delete (optional)

### Example Actions
*   `DeleteUserAction`
*   `RestoreUserAction`
*   `ForceDeleteUserAction`

## 16.13 Controllers (Admin)

Same strict rules apply:

*   ✔ Thin
*   ✔ No logic
*   ✔ Use DTO
*   ✔ Use `ApiResponserTrait`

### Example
```php
class AdminUserController extends Controller
{
    public function index(GetUsersRequest $request, GetUsersAction $action)
    {
        return $this->paginated(
            AdminUserResource::collection(
                $action->execute(
                    GetUsersDTO::fromRequest($request)
                )
            )
        );
    }
}
```

## 16.14 Resources (Admin)

### Rule

Admin resources MAY differ from user resources

Example:

Admin sees:
*   `email`
*   `status`
*   `roles`

User sees limited data

## 16.15 Security Rules

Required:

*   ✔ Role must be `admin` to access `/admin/*`
*   ✔ Permissions must be enforced
*   ✔ Unauthorized access → 403

### ❌ Forbidden
*   Exposing admin endpoints to normal users
*   Skipping permission checks

## 16.16 Golden Flow (Admin)

```plaintext
Request
 → FormRequest
 → Admin DTO
 → Admin Action
 → (optional) Core Action
 → Repository
 → Resource
 → ApiResponserTrait
```

---

# Final Note

This architecture is **strict by design**.

If a feature does not fit:

* Do NOT break the rules
* Extend the architecture properly

Consistency > convenience.