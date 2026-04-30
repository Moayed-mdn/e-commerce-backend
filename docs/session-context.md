# Session Context — Multi-Store Refactor

## Project
Laravel e-commerce backend → converting to Shopify-like 
multi-store (multi-tenant) architecture.

## What Is DONE ✅
- Phase 1: Database migrations (stores, store_user, 
  store_id on all tables)
- Phase 2: Models (Store model, relationships, 
  StoreContext middleware, exceptions, ErrorCodes)
- Phase 3: DTOs + Repositories (all store-scoped)
- Phase 3.5: Services (ProductService, AddressService, 
  BestSellerService all pass storeId)
- Phase 4: Routes refactored to /api/v1/stores/{store}/

## What Is MISSING 🔴
1. Address routes — refactored but NOT re-routed
   → Need: /api/v1/stores/{store}/addresses/*
   
2. Checkout/Payment routes — NOT re-routed
   → Need: /api/v1/stores/{store}/checkout/*

3. Store Management APIs — does not exist yet
   → Need: POST /api/v1/stores (create store)
   → Need: GET /api/v1/stores/{store} (get store)
   → Need: PUT /api/v1/stores/{store} (update store)

4. Store Seeder — no way to create test store in DB

5. Spatie IS installed (v7.4.1) ✅
   → Need: publish + migrate + seed roles

## Tech Stack
- Laravel 12
- Spatie Permission v7.4.1 (installed, NOT configured)
- Sanctum (auth)
- Stripe (payments)
- PHP 8+

## Architecture Rules File
→ See: rules file (share in new session)

## Current Routes Working
- /api/v1/stores/{store}/cart/*        ✅
- /api/v1/stores/{store}/orders/*      ✅
- /api/v1/stores/{store}/products/*    ✅
- /api/v1/users/auth/*                 ✅
- /api/v1/users/profile/*              ✅
- /api/v1/users/orders/guest/lookup    ✅
- /api/v1/users/homepage/*             ✅
- /api/v1/users/search                 ✅
- /api/stripe/webhook                  ✅

## Current Routes MISSING
- /api/v1/stores/{store}/addresses/*   ❌
- /api/v1/stores/{store}/checkout/*    ❌
- /api/v1/stores                       ❌

## Key Architecture Decisions
- storeId MUST come from route parameter {store}
- All DTOs have storeId as FIRST parameter
- All repositories scope by store_id
- Controllers under Http/Controllers/Api/
- Resources are flat (not domain-grouped) except Admin
- Spatie teams feature for store-scoped permissions

## Next Steps (in order)
1. Fix address routes
2. Fix checkout routes  
3. Create store management API
4. Configure Spatie + seed roles/permissions
5. Create store seeder
6. Test all endpoints
7. Then → Frontend
