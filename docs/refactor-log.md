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
## Enforce Strict Multi-Store Scoping in Repositories and Actions — 2026-04-29
### What I Did:
- Updated OrderRepository to add store_id scoping to all methods
- Updated ProductRepository to add store_id scoping to all methods
- Updated ProductVariantRepository to add store_id scoping to all methods
- Updated AddressRepository to add store_id scoping to all methods
- Updated CategoryRepository to add store_id scoping to all methods
- Updated CreateOrderAction to pass storeId to repository methods
- Updated GetOrderAction to pass storeId to repository methods
- Updated ListOrdersAction to pass storeId to repository methods
- Updated CancelOrderAction to pass storeId to repository methods
- Updated ListProductsAction to pass storeId to ProductService calls
- Updated GetProductDetailAction to pass storeId to ProductService calls
- Updated FilterProductsAction to pass storeId to repository methods
- Updated FilterProductsByCategoryAction to pass storeId to repository methods
- Updated GetRelatedProductsAction to pass storeId to ProductService calls
- Updated GetBestSellersAction to pass storeId to BestSellerService calls
- Updated StoreAddressAction to pass storeId to AddressService calls
- Updated UpdateAddressAction to pass storeId to repository methods
- Updated DeleteAddressAction to pass storeId to repository methods
- Updated ListAddressesAction to pass storeId to AddressService calls
- Updated SetDefaultAddressAction to pass storeId to repository methods

### Files Created:
- None

### Files Modified:
- `app/Repositories/Order/OrderRepository.php` — Added int $storeId parameter to all methods, added store_id scope to all queries
- `app/Repositories/Product/ProductRepository.php` — Added int $storeId parameter to all methods, added store_id scope to buildBaseQuery and all queries
- `app/Repositories/Product/ProductVariantRepository.php` — Added int $storeId parameter to findById, findByIdWithProduct, findWithLock methods
- `app/Repositories/Address/AddressRepository.php` — Added int $storeId parameter to all methods, added store_id scope to all queries
- `app/Repositories/Category/CategoryRepository.php` — Added int $storeId parameter to all methods, added store_id scope to all queries
- `app/Actions/Order/CreateOrderAction.php` — Updated to use dto->storeId when accessing cart and creating order
- `app/Actions/Order/GetOrderAction.php` — Updated execute() to pass dto->storeId to findById()
- `app/Actions/Order/ListOrdersAction.php` — Updated execute() to pass dto->storeId to getUserOrders()
- `app/Actions/Order/CancelOrderAction.php` — Updated execute() to pass dto->storeId to findById()
- `app/Actions/Product/ListProductsAction.php` — Updated to pass storeId to ProductService methods
- `app/Actions/Product/GetProductDetailAction.php` — Updated to pass storeId to findProductBySlugOrFail()
- `app/Actions/Product/FilterProductsAction.php` — Updated to pass storeId to all repository methods
- `app/Actions/Product/FilterProductsByCategoryAction.php` — Updated to pass storeId to all repository methods
- `app/Actions/Product/GetRelatedProductsAction.php` — Updated to pass storeId to findProductBySlugOrFail()
- `app/Actions/Product/GetBestSellersAction.php` — Updated to pass storeId to getCachedAllParents()
- `app/Actions/Address/StoreAddressAction.php` — Updated to pass storeId to storeAddress()
- `app/Actions/Address/UpdateAddressAction.php` — Updated to pass storeId to unsetDefaultForType(), update(), setDefault()
- `app/Actions/Address/DeleteAddressAction.php` — Updated to pass storeId to getNextDefault(), delete()
- `app/Actions/Address/ListAddressesAction.php` — Updated to pass storeId to getUserAddresses()
- `app/Actions/Address/SetDefaultAddressAction.php` — Updated to pass storeId to setDefault()

### Migrations Created:
- None

### Notes:
- All repository methods now require int $storeId as a parameter
- All queries now include ->where('store_id', $storeId) scope
- Model::find() and Model::all() calls replaced with store-scoped queries
- withTrashed() queries also include store_id scope where applicable
- Actions updated to extract storeId from DTO and pass to repository/service methods
- Controllers, FormRequests, and Resources were NOT modified as per strict rules
- ProductRepository buildBaseQuery() now accepts storeId and applies it to the base query
- CategoryRepository methods now filter by store_id for all category lookups
- AddressRepository setDefault() and unsetDefaultForType() now include store_id in their queries
