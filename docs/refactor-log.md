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
