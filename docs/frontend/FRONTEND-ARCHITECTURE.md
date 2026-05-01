# Frontend Architecture (Core)

This document contains the core philosophy, tech stack, project structure, naming conventions, and golden flow for the frontend architecture.

---

# 1. Core Philosophy

- This is a pure admin dashboard frontend
- It connects to a Laravel + Sanctum REST API backend
- No server-side business logic lives in Next.js
- Next.js is responsible for: rendering, routing, auth, UI only
- Code must be predictable, scalable, and theme-switchable
- Every architectural decision must be traceable to a rule
  in this document

---

# 2. Tech Stack (FIXED — do not change)

| Concern          | Tool                          |
|------------------|-------------------------------|
| Framework        | Next.js 14+ (App Router)      |
| Language         | TypeScript (strict mode)      |
| Styling          | Tailwind CSS + shadcn/ui      |
| State Management | Zustand                       |
| Server Data      | React Server Components (RSC) |
| Client Data      | TanStack Query v5             |
| Auth             | Sanctum tokens + httpOnly cookies |
| HTTP Client      | Axios (typed instance)        |
| Forms            | React Hook Form + Zod         |
| Icons            | Lucide React                  |

---

# 3. Project Structure

```plaintext
src/
 ├── app/                        ← App Router (pages + layouts)
 │    ├── (auth)/                ← Auth group (login, etc.)
 │    │    ├── login/
 │    │    │    └── page.tsx
 │    │    └── layout.tsx
 │    ├── (admin)/               ← Admin group
 │    │    ├── stores/
 │    │    │    └── [store]/
 │    │    │         ├── dashboard/
 │    │    │         │    └── page.tsx
 │    │    │         ├── users/
 │    │    │         │    ├── page.tsx
 │    │    │         │    └── [user]/
 │    │    │         │         └── page.tsx
 │    │    │         ├── products/
 │    │    │         │    ├── page.tsx
 │    │    │         │    └── [product]/
 │    │    │         │         └── page.tsx
 │    │    │         ├── orders/
 │    │    │         │    ├── page.tsx
 │    │    │         │    └── [order]/
 │    │    │         │         └── page.tsx
 │    │    └── layout.tsx
 │    ├── layout.tsx             ← Root layout
 │    └── globals.css            ← CSS tokens live here
 │
 ├── components/
 │    ├── ui/                    ← shadcn/ui primitives (auto-generated)
 │    ├── common/                ← Shared across domains
 │    │    ├── DataTable/
 │    │    ├── PageHeader/
 │    │    ├── ConfirmDialog/
 │    │    └── StatusBadge/
 │    ├── admin/                 ← Domain-grouped admin components
 │    │    ├── users/
 │    │    ├── products/
 │    │    ├── orders/
 │    │    └── dashboard/
 │    └── layout/                ← Sidebar, Navbar, etc.
 │
 ├── lib/
 │    ├── api/                   ← Typed API layer
 │    │    ├── axios.ts          ← Axios instance
 │    │    ├── admin/
 │    │    │    ├── users.ts
 │    │    │    ├── products.ts
 │    │    │    ├── orders.ts
 │    │    │    └── dashboard.ts
 │    │    └── auth.ts
 │    ├── hooks/                 ← TanStack Query hooks
 │    │    ├── admin/
 │    │    │    ├── useUsers.ts
 │    │    │    ├── useProducts.ts
 │    │    │    ├── useOrders.ts
 │    │    │    └── useDashboard.ts
 │    │    └── useAuth.ts
 │    └── utils/                 ← Pure utility functions
 │
 ├── stores/                     ← Zustand stores
 │    ├── authStore.ts
 │    ├── storeStore.ts          ← Current active store context
 │    └── uiStore.ts             ← Sidebar state, modals, etc.
 │
 ├── types/                      ← Global TypeScript types
 │    ├── api.ts                 ← API response shapes
 │    ├── user.ts
 │    ├── product.ts
 │    ├── order.ts
 │    └── store.ts
 │
 └── middleware.ts               ← Auth protection middleware
```

---

# 12. Naming Conventions

| Thing | Convention | Example |
|-------|-----------|---------|
| Pages | lowercase folders | `app/(admin)/users/page.tsx` |
| Components | PascalCase | `UserTable.tsx` |
| Hooks | camelCase with `use` | `useUsers.ts` |
| Stores | camelCase with `Store` | `authStore.ts` |
| API functions | camelCase | `getUsers.ts` |
| Types | PascalCase | `AdminUser` |
| Zod schemas | camelCase with `Schema` | `createUserSchema` |

---

# 15. Golden Flow (Data)

## Read Flow
```
Page (RSC)
 → API function (typed)
 → Pass as prop to Client Component
 → TanStack Query for refetch/pagination
 → Component renders
```

## Write Flow
```
Form (React Hook Form + Zod)
 → TanStack Mutation
 → API function (typed)
 → On success: invalidate query + show toast
 → On error: map API errors to form fields
```

---

# Final Note

This architecture is strict by design.
If a feature does not fit — extend properly.
Consistency > convenience.
