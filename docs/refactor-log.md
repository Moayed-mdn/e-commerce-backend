---
## Multi-Store Architecture Migrations — 2026-04-28
### What I Did:
- Created docs/refactor-log.md to track all migration changes
- Created 2026_04_01_000001_create_stores_table.php migration for stores table
- Created 2026_04_01_000002_create_store_user_table.php migration for store-user pivot table
- Created 2026_04_01_000003_add_store_id_to_products_table.php migration
- Created 2026_04_01_000004_add_store_id_to_orders_table.php migration
- Created 2026_04_01_000005_add_store_id_to_carts_table.php migration
- Created 2026_04_01_000006_add_store_id_to_cart_items_table.php migration
- Created 2026_04_01_000007_add_store_id_to_addresses_table.php migration
- Created 2026_04_01_000008_add_store_id_to_reviews_table.php migration
- Created 2026_04_01_000009_add_store_id_to_categories_table.php migration

### Files Created:
- `docs/refactor-log.md` — Migration tracking log file
- `database/migrations/2026_04_01_000001_create_stores_table.php` — Creates stores table with owner_id, name, slug, is_active
- `database/migrations/2026_04_01_000002_create_store_user_table.php` — Creates store_user pivot table for store-user relationships with roles
- `database/migrations/2026_04_01_000003_add_store_id_to_products_table.php` — Adds nullable store_id foreign key to products table
- `database/migrations/2026_04_01_000004_add_store_id_to_orders_table.php` — Adds nullable store_id foreign key to orders table
- `database/migrations/2026_04_01_000005_add_store_id_to_carts_table.php` — Adds nullable store_id foreign key to carts table
- `database/migrations/2026_04_01_000006_add_store_id_to_cart_items_table.php` — Adds nullable store_id foreign key to cart_items table
- `database/migrations/2026_04_01_000007_add_store_id_to_addresses_table.php` — Adds nullable store_id foreign key to addresses table
- `database/migrations/2026_04_01_000008_add_store_id_to_reviews_table.php` — Adds nullable store_id foreign key to reviews table
- `database/migrations/2026_04_01_000009_add_store_id_to_categories_table.php` — Adds nullable store_id foreign key to categories table

### Files Modified:
- None

### Migrations Created:
- `2026_04_01_000001_create_stores_table.php` — Creates stores table with id, owner_id (FK to users), name, unique slug, is_active default true, softDeletes, timestamps; indexes on owner_id and is_active
- `2026_04_01_000002_create_store_user_table.php` — Creates store_user pivot table with id, store_id (FK to stores), user_id (FK to users), role enum (store_admin/staff), timestamps; unique index on [store_id, user_id]
- `2026_04_01_000003_add_store_id_to_products_table.php` — Adds nullable store_id FK with cascadeOnDelete to products table, indexes on store_id and [store_id, id]
- `2026_04_01_000004_add_store_id_to_orders_table.php` — Adds nullable store_id FK with cascadeOnDelete to orders table, indexes on store_id and [store_id, id]
- `2026_04_01_000005_add_store_id_to_carts_table.php` — Adds nullable store_id FK with cascadeOnDelete to carts table, indexes on store_id and [store_id, id]
- `2026_04_01_000006_add_store_id_to_cart_items_table.php` — Adds nullable store_id FK with cascadeOnDelete to cart_items table, indexes on store_id and [store_id, id]
- `2026_04_01_000007_add_store_id_to_addresses_table.php` — Adds nullable store_id FK with cascadeOnDelete to addresses table, indexes on store_id and [store_id, id]
- `2026_04_01_000008_add_store_id_to_reviews_table.php` — Adds nullable store_id FK with cascadeOnDelete to reviews table, indexes on store_id and [store_id, id]
- `2026_04_01_000009_add_store_id_to_categories_table.php` — Adds nullable store_id FK with cascadeOnDelete to categories table, indexes on store_id and [store_id, id]

### Notes:
- All migrations use nullable() for store_id to accommodate existing rows without store association
- All foreign keys use cascadeOnDelete() as per strict rules
- No existing migration files were modified
- No raw SQL or DB::statement used
- Indexes follow the exact pattern specified: single index on store_id and composite index on [store_id, id]
- Migration filenames follow the exact naming convention specified in the task
- store_id is placed after('id') in all tables as specified

---
## Strict Multi-Store Architecture Refactor — 2026-04-29
### What I Did:
- Created app/Models/Store.php model with all required relationships
- Updated app/Models/User.php to add stores() relationship, replace cart() with carts(), add cartForStore() helper
- Updated app/Models/Cart.php to add store_id to fillable and add store() relationship
- Updated app/Models/Order.php to add store_id to fillable and add store() relationship
- Updated app/Models/Product.php to add store_id to fillable and add store() relationship
- Updated app/Enums/ErrorCode.php to add STR_001 and STR_002 error codes
- Created app/Exceptions/Store/StoreNotFoundException.php exception class
- Created app/Exceptions/Store/UnauthorizedStoreAccessException.php exception class
- Updated lang/en/error.php to add store_not_found and unauthorized_store messages
- Updated lang/ar/error.php to add Arabic translations for store errors
- Created app/Http/Middleware/StoreContext.php middleware for store context resolution
- Updated bootstrap/app.php to register store.context middleware alias
- Created app/Actions/Store/ValidateStoreMembershipAction.php action for membership validation

### Files Created:
- `app/Models/Store.php` — New Store model with owner, users, products, orders, carts relationships
- `app/Exceptions/Store/StoreNotFoundException.php` — Exception thrown when store is not found or inactive (404)
- `app/Exceptions/Store/UnauthorizedStoreAccessException.php` — Exception thrown when user lacks store access (403)
- `app/Http/Middleware/StoreContext.php` — Middleware that resolves store from route and sets app context
- `app/Actions/Store/ValidateStoreMembershipAction.php` — Action that validates user membership in a store

### Files Modified:
- `app/Models/User.php` — Removed cart() hasOne, added stores() belongsToMany, added carts() hasMany, added cartForStore() helper method
- `app/Models/Cart.php` — Added 'store_id' to fillable array, added store() belongsTo relationship
- `app/Models/Order.php` — Added 'store_id' to fillable array (after order_number), added store() belongsTo relationship
- `app/Models/Product.php` — Added 'store_id' to fillable array (first item), added store() belongsTo relationship
- `app/Enums/ErrorCode.php` — Added STR_001 (Store not found) and STR_002 (Unauthorized store access) cases
- `lang/en/error.php` — Added 'store_not_found' and 'unauthorized_store' translation keys
- `lang/ar/error.php` — Added Arabic translations for 'store_not_found' and 'unauthorized_store'
- `bootstrap/app.php` — Added 'store.context' middleware alias to withMiddleware section

### Migrations Created:
- None (migrations were created in previous prompt)

### Notes:
- User model: cart() method was removed and replaced with carts() hasMany relationship as per TASK 2 requirements
- All existing methods in User, Cart, Order, and Product models were preserved
- ValidateStoreMembershipAction uses hasRole() which requires spatie/laravel-permission package
- StoreContext middleware only finds active stores (is_active = true)
- StoreNotFoundException and UnauthorizedStoreAccessException extend BaseApiException and use ErrorCode enum
- All new relationships use proper return type hints (\Illuminate\Database\Eloquent\Relations\*)
- The store.context middleware stores both 'storeId' and 'currentStore' in the service container for later retrieval

---
## Enforce Strict Multi-Store Scoping in Repositories and DTOs — 2026-05-02
### What I Did:
- Updated all Cart DTOs to include storeId as first constructor parameter
- Updated all Order DTOs to include storeId as first constructor parameter
- Updated all Product DTOs to include storeId as first constructor parameter
- Updated all Address DTOs to include storeId as first constructor parameter
- Updated CartRepository to enforce store_id scoping in all methods
- Updated all Cart Actions to pass storeId to repository methods

### Files Created:
- None

### Files Modified:
- `app/DTOs/Cart/AddToCartDTO.php` — Added storeId as first parameter, updated fromRequest() signature
- `app/DTOs/Cart/GetCartDTO.php` — Added storeId as first parameter, updated fromRequest() signature
- `app/DTOs/Cart/ClearCartDTO.php` — Added storeId as first parameter, updated fromRequest() signature
- `app/DTOs/Cart/RemoveCartItemDTO.php` — Added storeId as first parameter, updated fromRequest() signature
- `app/DTOs/Cart/UpdateCartItemDTO.php` — Added storeId as first parameter, updated fromRequest() signature
- `app/Repositories/Cart/CartRepository.php` — Rewrote all methods to require storeId and use store-scoped queries
- `app/Actions/Cart/AddToCartAction.php` — Updated execute() to pass dto->storeId to repository
- `app/Actions/Cart/GetCartAction.php` — Updated execute() to pass dto->storeId to repository
- `app/Actions/Cart/ClearCartAction.php` — Updated execute() to pass dto->storeId to repository
- `app/Actions/Cart/RemoveCartItemAction.php` — Updated execute() to pass dto->storeId to repository (via cart lookup)
- `app/Actions/Cart/UpdateCartItemAction.php` — Updated execute() to pass dto->storeId to repository (via cart lookup)
- `app/DTOs/Order/CreateOrderDTO.php` — Added storeId as first parameter, updated fromRequest() signature
- `app/DTOs/Order/GetOrderDTO.php` — Added storeId as first parameter, updated fromRequest() signature
- `app/DTOs/Order/ListOrdersDTO.php` — Added storeId as first parameter, updated fromRequest() signature
- `app/DTOs/Order/CancelOrderDTO.php` — Added storeId as first parameter, updated fromRequest() signature
- `app/DTOs/Product/ListProductsDTO.php` — Added storeId as first parameter, updated fromRequest() signature
- `app/DTOs/Product/GetProductDetailDTO.php` — Added storeId as first parameter, updated fromRequest() signature
- `app/DTOs/Product/FilterProductsDTO.php` — Added storeId as first parameter, updated fromRequest() signature
- `app/DTOs/Product/FilterProductsByCategoryDTO.php` — Added storeId as first parameter, updated fromRequest() signature
- `app/DTOs/Product/GetRelatedProductsDTO.php` — Added storeId as first parameter, updated fromRequest() signature
- `app/DTOs/Product/GetBestSellersDTO.php` — Added storeId as first parameter, updated fromRequest() signature
- `app/DTOs/Address/StoreAddressDTO.php` — Added storeId as first parameter, updated fromRequest() signature
- `app/DTOs/Address/UpdateAddressDTO.php` — Added storeId as first parameter, updated fromRequest() signature
- `app/DTOs/Address/DeleteAddressDTO.php` — Added storeId as first parameter, updated fromRequest() signature
- `app/DTOs/Address/ListAddressesDTO.php` — Added storeId as first parameter, updated fromRequest() signature
- `app/DTOs/Address/SetDefaultAddressDTO.php` — Added storeId as first parameter, updated fromRequest() signature

### Migrations Created:
- None

### Notes:
- All DTOs now have storeId as the FIRST constructor parameter as per architecture rules
- All fromRequest() methods now accept int $storeId as second parameter after the request
- Named arguments are used in DTO constructors for clarity
- CartRepository methods now require int $storeId parameter and use it in all queries
- Cart Actions now extract storeId from DTO and pass it to repository methods
- RemoveCartItemAction and UpdateCartItemAction still need cart-based scoping since they work with itemId
- Controllers, FormRequests, and Resources were NOT modified as per strict rules
- Product DTOs that had slug as first parameter now have storeId first, slug second
- FilterProductsByCategoryDTO had slug as first positional param, now storeId is first

---
## Update Services to Accept and Pass StoreId — 2026-04-29
### What I Did:
- Updated ProductService to accept storeId in methods that call repositories
- Updated BestSellerService to accept storeId in methods that call repositories
- Updated AddressService to accept storeId in methods that call repositories
- Passed storeId to all repository method calls within services

### Files Created:
- None

### Files Modified:
- `app/Services/ProductService.php` — Added int $storeId parameter to buildBaseProductQuery(), findCategoryBySlug(), findCategoryBySlugOrFail(), findProductBySlug(), findProductBySlugOrFail(), getRelatedProducts(); passed storeId to repository calls
- `app/Services/BestSellerService.php` — Added int $storeId parameter to buildDescendantMap(), computeAllProductSales(), buildBestSellersForAllParents(), getCachedForParentId(); passed storeId to Category queries
- `app/Services/AddressService.php` — Added int $storeId parameter to getUserAddresses(), storeAddress(), updateAddress(), deleteAddress(), setAsDefault(); passed storeId to repository calls

### Migrations Created:
- None

### Notes:
- ProductService buildBaseProductQuery() now accepts storeId and passes it to ProductRepository::buildBaseQuery()
- ProductService getCategoryDescendants() now accepts storeId and passes it to CategoryRepository::getRootCategories()
- ProductService findCategoryBySlug() and findCategoryBySlugOrFail() now accept storeId parameter
- ProductService findProductBySlug() and findProductBySlugOrFail() now accept storeId parameter
- ProductService getRelatedProducts() now accepts storeId and passes it to ProductRepository::findRelatedProducts()
- BestSellerService buildDescendantMap() now accepts storeId for future scoping (currently builds full map)
- BestSellerService computeAllProductSales() now accepts storeId for filtering by store
- BestSellerService buildBestSellersForAllParents() now accepts storeId as first parameter
- BestSellerService getCachedForParentId() now accepts storeId parameter
- AddressService getUserAddresses() now accepts storeId as first parameter after userId
- AddressService storeAddress() now extracts storeId from DTO and passes to repository
- AddressService updateAddress() now passes storeId to setAsDefaultForType() and update()
- AddressService deleteAddress() now passes storeId to repository delete method
- AddressService setAsDefault() now accepts storeId and passes to repository
- All service methods maintain existing business logic, only adding storeId parameter passing

---
## Refactor routes and update controller method signatures for multi-store architecture — 2026-04-29
### What I Did:
- Created new route directory structure routes/api/v1/stores/
- Created routes/api/v1/stores/cart.php with store-scoped cart routes
- Created routes/api/v1/stores/orders.php with store-scoped order routes
- Created routes/api/v1/stores/products.php with store-scoped product routes
- Updated routes/api.php to use new route structure
- Updated CartController method signatures to accept int $store parameter
- Updated OrderController method signatures to accept int $store parameter
- Updated ProductController method signatures to accept int $store parameter

### Files Created:
- `routes/api/v1/stores/cart.php` — Store-scoped cart routes with auth:sanctum and store.context middleware
- `routes/api/v1/stores/orders.php` — Store-scoped order routes with guest lookup endpoint
- `routes/api/v1/stores/products.php` — Store-scoped product routes with store.context middleware

### Files Modified:
- `routes/api.php` — Replaced old route includes with new structure, removed debug routes
- `app/Http/Controllers/Api/Cart/CartController.php` — Added int $store parameter to all methods, passed to DTOs
- `app/Http/Controllers/Api/Order/OrderController.php` — Added int $store parameter to authenticated methods
- `app/Http/Controllers/Api/Product/ProductController.php` — Added int $store parameter to all methods

### Migrations Created:
- None

### Notes:
- Guest order lookup route remains without store context as specified
- Auth routes, webhook routes, homepage/category/search/profile routes remain unchanged
- int $store parameter placement: after Request parameter, before other parameters (orderNumber, slug, etc.)
- CartController updateItem and removeItem methods now pass $store to internal show() calls
- All controllers maintain constructor injection intact
- No business logic was added to controllers
- Route prefixes changed from /v1/users/* to /v1/stores/{store} for store-scoped routes
- Debug routes /test and /test-mailtrap were removed from api.php

---
## Fix Address Routes and Controller — 2026-04-29
### What I Did:
- Created routes/api/v1/stores/addresses.php with all 5 address routes under store scope
- Registered addresses.php in routes/api.php alongside cart, orders, and products
- Updated AddressController to accept int $store parameter in all public methods
- Added Request and JsonResponse type imports to AddressController
- Removed unused SetDefaultAddressRequest import from AddressController
- Passed $store to StoreAddressDTO::fromRequest() and UpdateAddressDTO::fromRequest()

### Files Created:
- `routes/api/v1/stores/addresses.php` — Store-scoped address routes with auth:sanctum and store.context middleware

### Files Modified:
- `routes/api.php` — Added require for addresses.php route file
- `app/Http/Controllers/Api/Address/AddressController.php` — Added int $store parameter to index(), store(), update(), destroy(), setDefault() methods; updated DTO calls to pass $store

### Migrations Created:
- None

### Notes:
- Route names use prefix stores.addresses. as specified
- All routes are under /api/v1/stores/{store}/addresses prefix
- Middleware auth:sanctum and store.context applied to all address routes
- Controller remains thin with no business logic added
- No try/catch blocks added to controller
- All responses use $this->success() or $this->paginated() via ApiResponserTrait
- The {address} route parameter uses Laravel model binding with Address model

---
## Fix Checkout Routes and Controller — 2026-04-29
### What I Did:
- Audited existing checkout controller and found CheckoutController with createSession() and status() methods
- Created routes/api/v1/stores/checkout.php with initiate and confirm routes under store scope
- Registered checkout.php in routes/api.php alongside cart, orders, products, and addresses
- Updated CheckoutController to rename createSession() to initiate() and add int $store parameter
- Added confirm() method to handle payment confirmation with int $store parameter
- Updated CreateCheckoutDTO to include storeId as first constructor parameter
- Passed $store to CreateCheckoutDTO::fromRequest() in the initiate() method
- Left StripeWebhookController completely untouched as required

### Files Created:
- `routes/api/v1/stores/checkout.php` — Store-scoped checkout routes (initiate and confirm) with auth:sanctum and store.context middleware

### Files Modified:
- `routes/api.php` — Added require for checkout.php route file
- `app/Http/Controllers/Api/Payment/CheckoutController.php` — Renamed createSession() to initiate(), added int $store parameter to initiate() and confirm(), added Request import, passed $store to DTO
- `app/DTOs/Payment/CreateCheckoutDTO.php` — Added int $storeId as first constructor parameter, updated fromRequest() signature to accept storeId

### Migrations Created:
- None

### Notes:
- Route names use prefix stores.checkout. as specified (stores.checkout.initiate, stores.checkout.confirm)
- All routes are under /api/v1/stores/{store}/checkout prefix
- Middleware auth:sanctum and store.context applied to all checkout routes
- Controller remains thin with no business logic added
- No try/catch blocks added to controller
- All responses use $this->success() via ApiResponserTrait
- Stripe webhook route at /api/stripe/webhook was NOT modified
- StripeWebhookController was NOT modified
- The status() method was preserved but is not used by the new store-scoped routes (it's for guest checkout lookup)
- CreateCheckoutDTO now has storeId as first parameter following architecture pattern
---
## Create Store Management API — 2026-04-29
### What I Did:
- Created routes/api/v1/stores/store-management.php with POST, GET, and PUT store routes
- Registered store-management.php in routes/api.php at the /api/v1/ level (outside {store} group)
- Created CreateStoreDTO with name, slug, ownerId properties and fromRequest() method
- Created UpdateStoreDTO with storeId as first parameter, nullable name, slug, isActive properties
- Created CreateStoreRequest with validation rules for name and slug
- Created UpdateStoreRequest with sometimes rules for name, slug, is_active
- Created CreateStoreAction that uses StoreRepository to create stores
- Created UpdateStoreAction that uses StoreRepository to update stores
- Created StoreRepository with create(), findById(), and update() methods
- Created StoreResource exposing id, name, slug, is_active, owner_id, created_at
- Created StoreController with create(), show(), and update() methods

### Files Created:
- `routes/api/v1/stores/store-management.php` — Store management routes (POST /stores, GET /stores/{store}, PUT /stores/{store})
- `app/DTOs/Store/CreateStoreDTO.php` — DTO for creating stores with name, slug, ownerId
- `app/DTOs/Store/UpdateStoreDTO.php` — DTO for updating stores with storeId as first param
- `app/Http/Requests/Store/CreateStoreRequest.php` — Form request validation for store creation
- `app/Http/Requests/Store/UpdateStoreRequest.php` — Form request validation for store updates
- `app/Actions/Store/CreateStoreAction.php` — Action to create stores via repository
- `app/Actions/Store/UpdateStoreAction.php` — Action to update stores via repository
- `app/Repositories/Store/StoreRepository.php` — Repository for store CRUD operations with transaction support
- `app/Http/Resources/Store/StoreResource.php` — API resource for store responses
- `app/Http/Controllers/Api/Store/StoreController.php` — Thin controller for store management endpoints

### Files Modified:
- `routes/api.php` — Added require for store-management.php route file outside the {store} group

### Migrations Created:
- None

### Notes:
- POST /api/v1/stores route has only auth:sanctum middleware (no store.context since store doesn't exist yet)
- GET and PUT /api/v1/stores/{store} routes have both auth:sanctum and store.context middleware
- Route names use prefix stores. (stores.create, stores.show, stores.update)
- StoreRepository::create() wraps store creation and pivot attachment in DB transaction
- StoreRepository::findById() throws StoreNotFoundException if store not found
- StoreRepository::update() only updates non-null fields from DTO
- StoreController::show() retrieves store from app('currentStore') resolved by store.context middleware
- All controllers remain thin with no business logic, no try/catch blocks
- All responses use $this->success() via ApiResponserTrait
- CreateStoreDTO::fromRequest() extracts ownerId from $request->user()->id
- UpdateStoreDTO::fromRequest() accepts storeId as second parameter from route
- CreateStoreAction attaches owner to store_user pivot with role 'store_admin'

---
## Configure Spatie Permissions + Seed Roles and Test Store — 2026-04-29
### What I Did:
- Updated config/permission.php to enable teams feature ('teams' => true)
- Created app/Enums/PermissionEnum.php with all permission constants
- Created app/Enums/RoleEnum.php with all role constants
- Created database/seeders/PermissionSeeder.php to seed permissions and roles
- Created database/seeders/StoreSeeder.php to seed test store and users
- Updated database/seeders/DatabaseSeeder.php to call PermissionSeeder and StoreSeeder
- Updated app/Models/User.php to add HasRoles trait from Spatie
- Updated app/Actions/Store/ValidateStoreMembershipAction.php to use RoleEnum constant

### Files Created:
- `app/Enums/PermissionEnum.php` — Defines all permission constants (user.*, product.*, order.*, store.*, dashboard.*)
- `app/Enums/RoleEnum.php` — Defines all role constants (super_admin, store_admin, staff, customer)
- `database/seeders/PermissionSeeder.php` — Seeds all permissions and roles, assigns permissions to roles using syncPermissions()
- `database/seeders/StoreSeeder.php` — Seeds test store and 4 test users with appropriate role assignments and pivot attachments

### Files Modified:
- `config/permission.php` — Changed 'teams' from false to true for store-scoped permissions
- `app/Models/User.php` — Added Spatie\Permission\Traits\HasRoles trait to the use statement and trait list
- `app/Actions/Store/ValidateStoreMembershipAction.php` — Replaced hardcoded 'super_admin' string with RoleEnum::SUPER_ADMIN constant
- `database/seeders/DatabaseSeeder.php` — Added PermissionSeeder::class and StoreSeeder::class at the beginning of the call array

### Migrations Created:
- None (Spatie migrations already exist at database/migrations/2026_04_29_150355_create_permission_tables.php)

### Notes:
- super_admin role gets ALL permissions
- store_admin role gets user, product, order, store, and dashboard permissions (no store.delete)
- staff role gets limited permissions: product.view, order.view, order.update_status, dashboard.view
- customer role has no permissions assigned
- For store-scoped roles (store_admin, staff), roles are assigned with team scope using $store->id
- For global roles (super_admin, customer), roles are assigned without team scope
- All users except customer are attached to the test store via store_user pivot table
- Passwords for all test users are bcrypt('password')
- No hardcoded role or permission strings anywhere — all use PermissionEnum and RoleEnum constants
