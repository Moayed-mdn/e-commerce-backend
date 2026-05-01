# Frontend Architecture Rules (Project Contract) 
 
 This document defines the mandatory architecture for the Next.js 
 frontend of this project. All contributors (human or AI) MUST 
 follow these rules strictly. 
 
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
 
 # 4. Color Token System (CRITICAL) 
 
 ## Problem This Solves 
 
 The client has not finalized colors. This system allows the 
 entire app color scheme to be changed by editing ONE file. 
 
 ## How It Works 
 
 All colors are defined as CSS variables in `globals.css`. 
 Tailwind is configured to read those variables. 
 Components NEVER use hardcoded Tailwind color classes like 
 `bg-blue-500` or `text-gray-900`. 
 They ALWAYS use semantic token classes like 
 `bg-primary` or `text-foreground`. 
 
 ## Token Definitions (`src/app/globals.css`) 
 
 ```css 
 @layer base { 
   :root { 
     /* Brand */ 
     --color-primary:         221 83% 53%; 
     --color-primary-hover:   221 83% 45%; 
     --color-primary-fore:    0 0% 100%; 
 
     /* Backgrounds */ 
     --color-bg:              0 0% 100%; 
     --color-surface:         0 0% 98%; 
     --color-surface-raised:  0 0% 96%; 
 
     /* Sidebar */ 
     --color-sidebar-bg:      222 47% 11%; 
     --color-sidebar-fore:    210 40% 96%; 
     --color-sidebar-active:  221 83% 53%; 
     --color-sidebar-border:  217 33% 17%; 
 
     /* Borders */ 
     --color-border:          214 32% 91%; 
     --color-border-strong:   214 32% 80%; 
 
     /* Text */ 
     --color-foreground:      222 47% 11%; 
     --color-muted:           215 16% 47%; 
     --color-subtle:          215 16% 65%; 
 
     /* Status */ 
     --color-success:         142 71% 45%; 
     --color-success-bg:      142 71% 95%; 
     --color-warning:         38 92% 50%; 
     --color-warning-bg:      38 92% 95%; 
     --color-danger:          0 84% 60%; 
     --color-danger-bg:       0 84% 95%; 
     --color-info:            199 89% 48%; 
     --color-info-bg:         199 89% 95%; 
   } 
 
   .dark { 
     --color-bg:              222 47% 11%; 
     --color-surface:         217 33% 17%; 
     --color-surface-raised:  215 28% 22%; 
     --color-border:          217 33% 25%; 
     --color-foreground:      210 40% 96%; 
     --color-muted:           215 16% 65%; 
   } 
 } 
 ``` 
 
 ## Tailwind Config (`tailwind.config.ts`) 
 
 ```ts 
 const config = { 
   theme: { 
     extend: { 
       colors: { 
         primary: { 
           DEFAULT: 'hsl(var(--color-primary))', 
           hover:   'hsl(var(--color-primary-hover))', 
           fore:    'hsl(var(--color-primary-fore))', 
         }, 
         bg:       'hsl(var(--color-bg))', 
         surface:  'hsl(var(--color-surface))', 
         raised:   'hsl(var(--color-surface-raised))', 
         border:   'hsl(var(--color-border))', 
         fore:     'hsl(var(--color-foreground))', 
         muted:    'hsl(var(--color-muted))', 
         subtle:   'hsl(var(--color-subtle))', 
         sidebar: { 
           bg:     'hsl(var(--color-sidebar-bg))', 
           fore:   'hsl(var(--color-sidebar-fore))', 
           active: 'hsl(var(--color-sidebar-active))', 
           border: 'hsl(var(--color-sidebar-border))', 
         }, 
         success: { 
           DEFAULT: 'hsl(var(--color-success))', 
           bg:      'hsl(var(--color-success-bg))', 
         }, 
         warning: { 
           DEFAULT: 'hsl(var(--color-warning))', 
           bg:      'hsl(var(--color-warning-bg))', 
         }, 
         danger: { 
           DEFAULT: 'hsl(var(--color-danger))', 
           bg:      'hsl(var(--color-danger-bg))', 
         }, 
         info: { 
           DEFAULT: 'hsl(var(--color-info))', 
           bg:      'hsl(var(--color-info-bg))', 
         }, 
       }, 
     }, 
   }, 
 } 
 ``` 
 
 ## Rules 
 
 - NEVER use hardcoded Tailwind color classes 
 - ALWAYS use semantic token classes 
 - When client decides on colors → edit `globals.css` only 
 - Dark mode is handled by `.dark` class on `<html>` 
 
 ### ❌ Forbidden 
 ```tsx 
 <div className="bg-blue-500 text-gray-900"> 
 <div className="border-gray-200"> 
 ``` 
 
 ### ✅ Required 
 ```tsx 
 <div className="bg-primary text-primary-fore"> 
 <div className="border-border"> 
 ``` 
 
 --- 
 
 # 5. API Layer 
 
 ## Axios Instance (`src/lib/api/axios.ts`) 
 
 One typed Axios instance. All API calls go through it. 
 
 ```ts 
 const api = axios.create({ 
   baseURL:         `${process.env.NEXT_PUBLIC_API_URL}/api/v1`, 
   withCredentials: true, 
   timeout:         10_000, 
   headers: { 
     Accept:         'application/json', 
     'Content-Type': 'application/json', 
   }, 
 }); 
 ``` 
 
 ## How Sanctum Auth Works 
 
 - Authentication uses httpOnly cookies 
 - Cookies are sent automatically by the browser 
   via `withCredentials: true` 
 - No manual token attachment is needed or allowed 
 - Never attempt to read httpOnly cookies client-side 
 
 Interceptors handle: 
 - Redirecting to login on 401 response 
 - Normalizing all errors through `normalizeError()` 
 - Formatting error responses consistently 
 
 ## API Layer Split 
 
 The API layer is split into two environments: 
 
 ```plaintext 
 src/lib/api/ 
  ├── client/          ← Axios (browser + client components only) 
  │    ├── axios.ts    ← Axios instance 
  │    ├── error.ts    ← Error normalizer 
  │    └── admin/ 
  │         ├── users.ts 
  │         ├── products.ts 
  │         ├── orders.ts 
  │         └── dashboard.ts 
  └── server/          ← fetch (RSC + server components only) 
       └── admin/ 
            ├── users.ts 
            ├── products.ts 
            ├── orders.ts 
            └── dashboard.ts 
 ``` 
 
 ### Client API (Axios — browser only) 
 
 ```ts 
 // src/lib/api/client/admin/users.ts 
 export const getUsers = async ( 
   storeId: number, 
   params: GetUsersParams, 
 ): Promise<PaginatedResponse<AdminUser>> => { 
   const { data } = await api.get( 
     `/admin/stores/${storeId}/users`, 
     { params }, 
   ); 
   return data; 
 }; 
 ``` 
 
 ### Server API (fetch — RSC only)

```ts
// src/lib/api/server/admin/users.ts
import { cookies } from 'next/headers';

export async function getUsersServer(
  storeId: number,
  params: GetUsersParams,
): Promise<PaginatedResponse<AdminUser>> {
  const cookieStore = cookies();

  const res = await fetch(
    `${process.env.API_URL}/api/v1/admin/stores/${storeId}/users`,
    {
      headers: {
        Cookie:  cookieStore.toString(),
        Accept:  'application/json',
      },
      cache: 'no-store',
    },
  );

  if (!res.ok) {
    throw new Error(`Server fetch failed: ${res.status}`);
  }

  return res.json();
}
```

## Server API Rules

- Server API MUST forward cookies manually using
  `cookies()` from `next/headers`
- NEVER rely on `credentials: 'include'` alone in RSC
  — it does not work for cross-origin APIs
- `credentials: 'include'` is for same-origin only
- Always set `cache: 'no-store'` for admin data
- Always check `res.ok` before parsing response

## Rules

- RSC MUST use server API layer only (`lib/api/server/`)
- Client components MUST use Axios layer only (`lib/api/client/`)
- NEVER use Axios inside RSC — it is browser-only
- NEVER use server API inside client components 
 
 ## Rules 
 
 - All API functions are typed with TypeScript 
 - All API functions live in `src/lib/api/` 
 - No fetch/axios calls inside components 
 - No fetch/axios calls inside Zustand stores 
 - API base URL comes from `NEXT_PUBLIC_API_URL` env variable 
 
 --- 
 
 # 6. Data Fetching Strategy 
 
 ## When to Use RSC (React Server Components) 
 
 Use RSC when: 
 - Data is needed for initial page render 
 - Data is SEO-critical 
 - Data does not change based on user interaction 
 - Page-level data fetching (dashboard stats on load) 
 
 ```tsx 
 // app/(admin)/stores/[store]/dashboard/page.tsx 
  "semi":         true,
  "singleQuote":  true,
  "tabWidth":     2,
  "trailingComma": "all"
}
```
Pre-commit Checks (via husky + lint-staged)
```text
TypeScript → zero errors
ESLint     → zero errors
Prettier   → auto-format
```
CI Rules
Build MUST pass before any merge
TypeScript MUST have zero errors
ESLint MUST have zero errors
No `console.log` in committed code
# 43. Corrected Rule — useEffect
Replace the absolute rule from Section 14 with this:

useEffect Rules
```text
useEffect is FORBIDDEN for:
- Data fetching (use RSC or TanStack Query)
- Derived state (use useMemo)
- Event handling (use event handlers directly)

useEffect is ALLOWED for:
- DOM subscriptions (resize, scroll observers)
- Third-party library initialization
- Cleanup on unmount
```
---
   user:      AdminUser | null; 
   storeId:   number | null; 
   isAuth:    boolean; 
   setUser:   (user: AdminUser) => void; 
   setStore:  (storeId: number) => void; 
   logout:    () => void; 
 } 
 ``` 
 
 ## Middleware (`src/middleware.ts`) 
 
 Protects all routes under `/(admin)`. 
 Redirects to `/login` if no valid session. 
 
 ## Rules 
 
 - Token NEVER stored in localStorage 
 - Token NEVER stored in sessionStorage 
 - Token ALWAYS in httpOnly cookie 
 - Auth state in Zustand (user info only — not token) 
 - Every admin page is protected by middleware 
 
 --- 
 
 # 8. Zustand Stores 
 
 Three stores only: 
 
 | Store | Purpose | 
 |-------|---------| 
 | `authStore` | Current user, permissions, logout | 
 | `storeStore` | Currently selected store context | 
 | `uiStore` | Sidebar open/close, active modals | 
 
 ## Rules 
 
 - Zustand stores are client-side only 
 - No API calls inside Zustand stores 
 - No business logic inside Zustand stores 
 - Stores hold UI state and session state only 
 - Use `persist` middleware for auth and store context 
 
 --- 
 
 # 9. Component Rules 
 
 ## Component Layers 
 
 | Layer | Location | Purpose | 
 |-------|----------|---------| 
 | shadcn primitives | `components/ui/` | Raw UI (Button, Input, etc.) | 
 | Common components | `components/common/` | Shared across domains | 
 | Domain components | `components/admin/{domain}/` | Domain-specific UI | 
 | Page components | `app/(admin)/...` | Route pages | 
 
 ## Rules 
 
 - shadcn components in `components/ui/` are NEVER modified directly 
 - Override via token system or wrapper components 
 - Common components are fully reusable and have no domain logic 
 - Domain components belong to one domain only 
 - Page files (`page.tsx`) are thin — they compose components only 
 - No inline styles — use Tailwind classes only 
 - No hardcoded color classes — use token classes only 
 
 ## Component File Structure 
 
 ```tsx 
 // Every component follows this structure: 
 
 // 1. Imports 
 // 2. Types 
 // 3. Component 
 // 4. Export 
 
 interface Props { 
   storeId: number; 
   userId:  number; 
 } 
 
 export function UserCard({ storeId, userId }: Props) { 
   // ... 
 } 
 ``` 
 
 --- 
 
 # 10. TypeScript Rules 
 
 - Strict mode ON — no exceptions 
 - No `any` type — ever 
 - All API responses have typed interfaces in `src/types/` 
 - All component props have explicit interfaces 
 - All Zustand stores have explicit interfaces 
 
 ## API Response Types (`src/types/api.ts`) 
 
 ```ts 
 export interface ApiResponse<T> { 
   status:  boolean; 
   message: string; 
   data:    T; 
 } 
 
 export interface PaginatedResponse<T> { 
   status:  boolean; 
   message: string; 
   data:    T[]; 
   meta: { 
     current_page: number; 
     last_page:    number; 
     per_page:     number; 
     total:        number; 
   }; 
 } 
 
 export interface ApiError { 
   status:     false; 
   message:    string; 
   error_code: string; 
   errors:     Record<string, string[]> | null; 
 } 
 ``` 
 
 --- 
 
 # 11. Forms 
 
 All forms use React Hook Form + Zod. 
 
 ```ts 
 const schema = z.object({ 
   name:  z.string().min(1, 'Name is required'), 
   email: z.string().email('Invalid email'), 
 }); 
 
 type FormData = z.infer<typeof schema>; 
 ``` 
 
 ## Rules 
 
 - No uncontrolled inputs 
 - All validation via Zod schemas 
 - Form submission via TanStack Query mutations 
 - Error messages from API mapped to form fields 
 
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
 
 # 13. Environment Variables 
 
 ```bash 
 NEXT_PUBLIC_API_URL=http://localhost:8000 
 NEXT_PUBLIC_APP_NAME=Admin Dashboard 
 ``` 
 
 - All public vars prefixed with `NEXT_PUBLIC_` 
 - Never expose secret keys on client side 
 - `.env.local` for local development 
 - `.env.production` for production 
 
 --- 
 
 # 14. Anti-Patterns (Forbidden) 
 
 - Hardcoded color classes (`bg-blue-500`, `text-gray-900`) 
 - Fetch/axios inside components 
 - Business logic in components 
 - `any` TypeScript type 
 - Token in localStorage or sessionStorage 
 - API calls inside Zustand stores 
 - useEffect for data fetching 
 - Inline styles 
 - Skipping TypeScript types on props 
 - Fat page components (pages compose — they do not logic) 
 
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
 
 # 16. Store Context Rule 
 
 Every admin page lives under `/stores/[store]/`. 
 The `[store]` param is the store ID. 
 It MUST be passed to every API call. 
 It MUST be stored in `storeStore` (Zustand). 
 It MUST never be hardcoded. 
 
 ```tsx 
 // Always read store from params or Zustand 
 const { storeId } = useStoreStore(); 
 ``` 
 
 ---
 
 # Final Note 
 
 This architecture is strict by design. 
 If a feature does not fit — extend properly. 
 Consistency > convenience. 
 
 --- 
 
 # 17. Error Handling Strategy 
 
 ## Global Errors 
 
 - Every route group MUST have an `error.tsx` file 
 - Root `app/error.tsx` catches unexpected global errors 
 - Error UI must be user-friendly — never show raw error messages 
 
 ## File Structure 
 
 ```plaintext 
 app/ 
  ├── error.tsx                    ← Global fallback 
  ├── (auth)/ 
  │    └── error.tsx 
  ├── (admin)/ 
  │    └── error.tsx 
 ``` 
 
 ## TanStack Query Errors 
 
 Every query MUST handle three states explicitly: 
 
 ```tsx 
 if (isLoading) return <TableSkeleton />; 
 if (isError)   return <ErrorState message={error.message} />; 
 if (!data)     return <EmptyState />; 
 ``` 
 
 ## Rules 
 
 - Never ignore `isError` 
 - Never render broken UI silently 
 - Never show raw API error messages to users 

---

# 51. Typed Filter Schemas

## Problem

Without typed schemas, URL query params become inconsistent
across pages — different types, different defaults,
different names for the same concept.

## Location

```plaintext
src/lib/filters/
 ├── users.filter.ts
 ├── products.filter.ts
 └── orders.filter.ts
```

## Example

```ts
// src/lib/filters/users.filter.ts
import { z } from 'zod';

export const usersFilterSchema = z.object({
  search:   z.string().optional(),
  status:   z.enum(['active', 'blocked']).optional(),
  page:     z.coerce.number().default(1),
  per_page: z.coerce.number().default(15),
});

export type UsersFilters = z.infer<typeof usersFilterSchema>;
```

```ts
// src/lib/filters/products.filter.ts
export const productsFilterSchema = z.object({
  search:      z.string().optional(),
  category_id: z.coerce.number().optional(),
  is_active:   z.enum(['true', 'false']).optional(),
  page:        z.coerce.number().default(1),
  per_page:    z.coerce.number().default(15),
});

export type ProductsFilters = z.infer<typeof productsFilterSchema>;
```

## Usage with nuqs

```ts
import { useQueryStates, parseAsString, parseAsInteger } from 'nuqs';
import { usersFilterSchema } from '@/lib/filters/users.filter';

export function useUsersFilters() {
  const [filters, setFilters] = useQueryStates({
    search:   parseAsString.withDefault(''),
    status:   parseAsString.withDefault(''),
    page:     parseAsInteger.withDefault(1),
    per_page: parseAsInteger.withDefault(15),
  });

  return { filters, setFilters };
}
```

## Rules

- ALL URL query params MUST be validated via Zod schemas
- Filter schemas live in `src/lib/filters/`
- No untyped query param usage anywhere
- Filters are domain-specific — no shared filter schemas
- Default values defined in schema — not in components

---

# 52. Axios Timeout & Request Cancellation

## Timeout Configuration

```ts
// src/lib/api/client/axios.ts
const api = axios.create({
  baseURL:         `${process.env.NEXT_PUBLIC_API_URL}/api/v1`,
  withCredentials: true,
  timeout:         10_000,   // 10 seconds
  headers: {
    Accept:         'application/json',
    'Content-Type': 'application/json',
  },
});
```

## Request Cancellation

For requests that may be superseded (search, filters):

```ts
export function useUsers(
  storeId: number,
  filters: UsersFilters,
) {
  return useQuery({
    queryKey: queryKeys.users(storeId, filters),
    queryFn:  ({ signal }) =>
      getUsers(storeId, filters, signal),
  });
}

// API function accepts signal
export const getUsers = async (
  storeId:  number,
  filters:  UsersFilters,
  signal?:  AbortSignal,
): Promise<PaginatedResponse<AdminUser>> => {
  const { data } = await api.get(
    `/admin/stores/${storeId}/users`,
    { params: filters, signal },
  );
  return data;
};
```

## Rules

- ALL Axios requests MUST have a 10 second timeout
- Search and filter queries MUST support cancellation
  via `AbortSignal` (TanStack Query passes `signal` automatically)
- Long-running uploads MAY have extended timeout
  (defined per-request, not globally)
- Timeout errors MUST be handled by `normalizeError()`

---

# 53. Client Error Boundaries

## Context

Next.js `error.tsx` files handle both RSC and client errors
at the route segment level. However, complex client components
should have isolated boundaries so one failure does not
crash the entire page.

## When to Use Isolated Error Boundaries

Wrap these components in their own ErrorBoundary:
- DataTable
- Charts and graphs
- Complex forms
- File upload components

## Implementation

```tsx
// components/common/ErrorBoundary.tsx
'use client';

import { Component, ReactNode } from 'react';
import { EmptyState } from './EmptyState';

interface Props {
  children:  ReactNode;
  fallback?: ReactNode;
}

interface State {
  hasError: boolean;
  message:  string;
}

export class ErrorBoundary extends Component<Props, State> {
  state = { hasError: false, message: '' };

  static getDerivedStateFromError(error: Error): State {
    return { hasError: true, message: error.message };
  }

  render() {
    if (this.state.hasError) {
      return this.props.fallback ?? (
        <EmptyState
          title="Something went wrong"
          description="Try refreshing the page"
        />
      );
    }
    return this.props.children;
  }
}
```

## Usage

```tsx
<ErrorBoundary>
  <DataTable columns={columns} data={data} />
</ErrorBoundary>

<ErrorBoundary fallback={<ChartErrorState />}>
  <RevenueChart storeId={storeId} />
</ErrorBoundary>
```

## Rules

- Route-level errors → handled by `error.tsx`
- Component-level errors → handled by `ErrorBoundary`
- DataTable MUST always be wrapped in ErrorBoundary
- Charts MUST always be wrapped in ErrorBoundary
- ErrorBoundary fallback MUST use `EmptyState` component
  or a domain-specific fallback — never blank

---

# 54. Mutation Responsibility Separation

## Rule

Mutation hooks handle data and cache only.
UI side effects belong in the component layer.

## What Hooks Own

- API call
- Cache invalidation
- Error normalization

## What Components Own

- Navigation after success
- Modal close after success
- Form reset after success
- Toast messages (called from onSuccess callback)

## Pattern

```ts
// Hook — data only
export function useBlockUser(storeId: number) {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: (userId: number) =>
      blockUser(storeId, userId),

    onSuccess: () => {
      queryClient.invalidateQueries({
        queryKey: queryKeys.users(storeId),
      });
    },
  });
}

// Component — UI side effects
export function BlockUserButton({ userId, storeId }) {
  const { mutate, isPending } = useBlockUser(storeId);
  const { t } = useTranslations('users');
  const router = useRouter();

  function handleBlock() {
    mutate(userId, {
      onSuccess: () => {
        toast.success(t('blocked_success'));
        router.push(ROUTES.USERS(storeId));
      },
      onError: (error: ApiError) => {
        toast.error(error.message);
      },
    });
  }
}
```

## Rules

- Hooks MUST NOT call `useRouter()` or navigate
- Hooks MUST NOT call `toast`
- Hooks MUST NOT call `form.reset()`
- Components call hooks and handle UI consequences
- `onSuccess` and `onError` callbacks passed from component

---

# 55. No Derived State Duplication

## Rule

Derived data MUST NOT be stored in state.
State duplication causes sync bugs.

## ❌ Forbidden

```ts
const [users, setUsers] = useState(data?.data ?? []);
const [total, setTotal] = useState(data?.meta.total ?? 0);

// Now you have two sources of truth — data and state
```

## ✅ Required

```ts
// Derive directly from source
const users = data?.data ?? [];
const total = data?.meta.total ?? 0;

// For expensive derivations
const activeUsers = useMemo(
  () => users.filter((u) => u.isActive),
  [users],
);
```

## Rules

- Never copy API response data into local state
- Never copy props into local state
- Use `useMemo` for expensive derived values
- Use direct derivation for simple values
- State is for user interaction only
  (form input, toggle open/closed, etc.)
 - All errors display via user-friendly UI components 
 - Network errors → show retry option 
 - 401 errors → redirect to login (handled in axios interceptor) 
 - 403 errors → show permission denied UI (not redirect) 
 - 404 errors → show not found UI 
 
 --- 
 
 # 18. Loading & Skeleton Standards 
 
 ## Rules 
 
 - Every async view MUST have a loading state 
 - Use skeletons instead of spinners for content areas 
 - Spinners are allowed for button loading states only 
 - No blank screens during loading 
 - No layout shift during loading 
 
 ## Loading Patterns by Context 
 
 | Context | Pattern | 
 |---------|---------| 
 | Full page | `loading.tsx` with page skeleton | 
 | Table | Skeleton rows (same count as per_page) | 
 | Stats cards | Skeleton cards | 
 | Single item | Skeleton detail layout | 
 | Button action | Spinner inside button, button disabled | 
 
 ## File Structure 
 
 ```plaintext 
 app/ 
  ├── (admin)/ 
  │    ├── loading.tsx              ← Admin group loading 
  │    └── stores/ 
  │         └── [store]/ 
  │              ├── loading.tsx 
  │              ├── users/ 
  │              │    └── loading.tsx 
  │              ├── products/ 
  │              │    └── loading.tsx 
  │              └── orders/ 
  │                   └── loading.tsx 
 ``` 
 
 ## Skeleton Components 
 
 All skeleton components live in `components/common/Skeletons/`: 
 
 ```plaintext 
 components/common/Skeletons/ 
  ├── TableSkeleton.tsx 
  ├── CardSkeleton.tsx 
  ├── DetailSkeleton.tsx 
  └── StatsSkeleton.tsx 
 ``` 
 
 --- 
 
 # 19. Authorization (Roles & Permissions) 
 
 ## How It Works 
 
 - Permissions come from the authenticated user object (backend) 
 - User object includes: `roles`, `permissions` 
 - Stored in `authStore` on login 
 - UI conditionally renders based on permissions 
 - Backend ALWAYS enforces — frontend is UI-only protection 
 
 ## Auth Store Permission Shape 
 
 ```ts 
 interface AuthStore { 
   user:        AdminUser | null; 
   permissions: string[]; 
   roles:       string[]; 
   isAuth:      boolean; 
   can:         (permission: string) => boolean; 
   hasRole:     (role: string) => boolean; 
 } 
 ``` 
 
 ## Permission Helper 
 
 ```ts 
 // Inside authStore 
 can: (permission: string) => { 
   return get().permissions.includes(permission); 
 } 
 
 hasRole: (role: string) => { 
   return get().roles.includes(role); 
 } 
 ``` 
 
 ## Usage in Components 
 
 ```tsx 
 const { can, hasRole } = useAuthStore(); 
 
 // Hide restricted UI 
 {can('user.delete') && ( 
   <DeleteUserButton userId={user.id} /> 
 )} 
 
 // Disable restricted actions 
 <Button disabled={!can('product.update')}> 
   Edit Product 
 </Button> 
 
 // Super admin check 
 {hasRole('super_admin') && ( 
   <GlobalStatsPanel /> 
 )} 
 ``` 
 
 ## Permission Constants (`src/config/permissions.ts`) 
 
 ```ts 
 export const PERMISSIONS = { 
   USER_VIEW:           'user.view', 
   USER_BLOCK:          'user.block', 
   USER_DELETE:         'user.delete', 
   USER_RESTORE:        'user.restore', 
   PRODUCT_VIEW:        'product.view', 
   PRODUCT_CREATE:      'product.create', 
   PRODUCT_UPDATE:      'product.update', 
   PRODUCT_DELETE:      'product.delete', 
   PRODUCT_RESTORE:     'product.restore', 
   ORDER_VIEW:          'order.view', 
   ORDER_UPDATE_STATUS: 'order.update_status', 
   ORDER_CANCEL:        'order.cancel', 
   ORDER_REFUND:        'order.refund', 
   DASHBOARD_VIEW:      'dashboard.view', 
 } as const; 
 ``` 
 
 ## Rules 
 
 - Never rely on frontend-only protection for security 
 - Always hide AND disable restricted actions 
 - Use `PERMISSIONS` constants — never hardcode strings 
 - Backend is the source of truth for permissions 
 
 --- 
 
 # 20. Domain Isolation 
 
 ## Rules 
 
 - Domains MUST NOT import from each other directly 
 - Shared logic goes to `components/common/` or `lib/utils/` 
 - No circular dependencies between domains 
 
 ## ❌ Forbidden 
 
 ```ts 
 // Inside products domain 
 import { UserCard } from '@/components/admin/users/UserCard'; 
 ``` 
 
 ## ✅ Required 
 
 ```ts 
 // Move shared component to common 
 import { UserCard } from '@/components/common/UserCard'; 
 ``` 
 
 ## Domain Boundaries 
 
 | Domain | Owns | 
 |--------|------| 
 | `admin/users` | User table, user detail, block/unblock UI | 
 | `admin/products` | Product table, product form, variant UI | 
 | `admin/orders` | Order table, order detail, status UI | 
 | `admin/dashboard` | Stats cards, charts, recent lists | 
 | `common` | DataTable, PageHeader, StatusBadge, Modals | 
 | `layout` | Sidebar, Navbar, Breadcrumbs | 
 
 --- 
 
 # 21. Mutation Pattern Standard 
 
 ## Naming Convention 
 
 ``` 
 use + Verb + Entity 
 
 useCreateUser 
 useUpdateProduct 
 useDeleteOrder 
 useBlockUser 
 useRestoreProduct 
 useUpdateOrderStatus 
 ``` 
 
 ## File Location 
 
 All mutation hooks live in `src/lib/hooks/admin/`: 
 
 ```plaintext 
 lib/hooks/admin/ 
  ├── useUsers.ts       ← queries + mutations for users 
  ├── useProducts.ts    ← queries + mutations for products 
  ├── useOrders.ts      ← queries + mutations for orders 
  └── useDashboard.ts   ← queries for dashboard 
 ``` 
 
 ## Mutation Pattern 
 
 ```ts 
 export function useBlockUser(storeId: number) { 
   const queryClient = useQueryClient(); 
 
   return useMutation({ 
     mutationFn: (userId: number) => 
       blockUser(storeId, userId), 
 
     onSuccess: () => { 
       queryClient.invalidateQueries({ 
         queryKey: ['admin', 'users', storeId], 
       }); 
       toast.success(t('users.blocked_success')); 
     }, 
 
     onError: (error: ApiError) => { 
       toast.error(error.message); 
     }, 
   }); 
 } 
 ``` 
 
 ## Rules 
 
 - All mutations live in `lib/hooks/` 
 - Always invalidate relevant queries on success 
 - Never manually mutate cache unless invalidation is impossible 
 - Always show success feedback via toast 
 - Always show error feedback via toast 
 - Map API validation errors to form fields when applicable 
 
 --- 
 
 # 22. Toast & Notification System 
 
 ## Tool 
 
 Use shadcn/ui `Toaster` component. 
 Initialized once in root layout. 
 
 ```tsx 
 // app/layout.tsx 
 import { Toaster } from '@/components/ui/toaster'; 
 
 export default function RootLayout({ children }) { 
   return ( 
     <html> 
       <body> 
         {children} 
         <Toaster /> 
       </body> 
     </html> 
   ); 
 } 
 ``` 
 
 ## Usage 
 
 ```ts 
 import { toast } from '@/components/ui/use-toast'; 
 
 toast.success(t('users.blocked_success')); 
 toast.error(t('errors.generic')); 
 toast.warning(t('common.irreversible')); 
 ``` 
 
 ## Rules 
 
 - No `alert()` anywhere 
 - No `console.log` for user feedback 
 - No custom notification systems — use shadcn Toaster only 
 - All success messages → `toast.success()` 
 - All error messages → `toast.error()` 
 - All warnings → `toast.warning()` 
 - Toast messages must be human-readable 
 - Never show raw API error codes in toasts 
 - Toast messages MUST use t() — no hardcoded strings 
 - Hardcoded toast messages are forbidden 
 
 --- 
 
 # 23. Routing & Navigation Rules 
 
 ## Rules 
 
 - Use `next/link` for all navigation links 
 - Use `useRouter()` only for programmatic navigation 
 - No hardcoded URL strings in components 
 - All routes defined in `src/config/routes.ts` 
 
 ## Route Config (`src/config/routes.ts`) 
 
 ```ts 
 export const ROUTES = { 
   LOGIN: '/login', 
 
   DASHBOARD: (storeId: number) => 
     `/stores/${storeId}/dashboard`, 
 
   USERS: (storeId: number) => 
     `/stores/${storeId}/users`, 
 
   USER_DETAIL: (storeId: number, userId: number) => 
     `/stores/${storeId}/users/${userId}`, 
 
   PRODUCTS: (storeId: number) => 
     `/stores/${storeId}/products`, 
 
   PRODUCT_DETAIL: (storeId: number, productId: number) => 
     `/stores/${storeId}/products/${productId}`, 
 
   ORDERS: (storeId: number) => 
     `/stores/${storeId}/orders`, 
 
   ORDER_DETAIL: (storeId: number, orderId: number) => 
     `/stores/${storeId}/orders/${orderId}`, 
 } as const; 
 ``` 
 
 ## Usage 
 
 ```tsx 
 // ✅ Required 
 import { ROUTES } from '@/config/routes'; 
 <Link href={ROUTES.USERS(storeId)}>Users</Link> 
 
 // ❌ Forbidden 
 <Link href={`/stores/${storeId}/users`}>Users</Link> 
 ``` 
 
 --- 
 
 # 24. Config & Constants Layer 
 
 ## Structure 
 
 ```plaintext 
 src/config/ 
  ├── app.ts           ← App name, version, defaults 
  ├── routes.ts        ← All route definitions 
  ├── permissions.ts   ← Permission constants 
  └── query.ts         ← TanStack Query default config 
 ``` 
 
 ## App Config (`src/config/app.ts`) 
 
 ```ts 
 export const APP_CONFIG = { 
   name:           process.env.NEXT_PUBLIC_APP_NAME ?? 'Admin', 
   apiUrl:         process.env.NEXT_PUBLIC_API_URL  ?? '', 
   defaultPerPage: 15, 
   toastDuration:  4000, 
 } as const; 
 ``` 
 
 ## Query Config (`src/config/query.ts`) 
 
 ```ts 
 export const QUERY_CONFIG = { 
   staleTime: 1000 * 60 * 5,    // 5 minutes 
   retry:     1, 
   refetchOnWindowFocus: false, 
 } as const; 
 ``` 
 
 ## Rules 
 
 - No magic strings anywhere in code 
 - No magic numbers anywhere in code 
 - All constants live in `src/config/` 
 - Import from config — never inline 
 
 --- 
 
 # 25. Data Tables Standard 
 
 ## Required Features (every table must have) 
 
 - Pagination (backend-driven) 
 - Sorting (backend-driven) 
 - Search/filtering 
 - Empty state 
 - Loading skeleton state 
 - Row actions (view, edit, delete, etc.) 
 
 ## Shared DataTable Component 
 
 Lives at `components/common/DataTable/DataTable.tsx`. 
 
 All domain tables use this component — they only define columns. 
 
 ```tsx 
 // components/admin/users/UsersTable.tsx 
 import { DataTable } from '@/components/common/DataTable'; 
 import { columns }   from './columns'; 
 
 export function UsersTable({ storeId }: { storeId: number }) { 
   const { data, isLoading } = useUsers(storeId); 
 
   return ( 
     <DataTable 
       columns={columns} 
       data={data?.data ?? []} 
       pagination={data?.meta} 
       isLoading={isLoading} 
     /> 
   ); 
 } 
 ``` 
 
 ## Rules 
 
 - Never build a one-off table outside DataTable 
 - Pagination is ALWAYS backend-driven — no client-side pagination 
 - Empty state is ALWAYS shown — never render empty tables 
 - Column definitions live next to their domain component 
 
 --- 
 
 # 26. URL State Synchronization 
 
 ## Rule 
 
 All table filters, search, and pagination MUST sync with URL 
 query parameters. Page reload must restore exact UI state. 
 
 ## Example URL 
 
 ``` 
 /stores/1/users?search=john&status=active&page=2&per_page=15 
 ``` 
 
 ## Implementation 
 
 Use `nuqs` library for type-safe URL state management. 
 
 ```ts 
 import { useQueryState } from 'nuqs'; 
 
 const [search,  setSearch]  = useQueryState('search'); 
 const [status,  setStatus]  = useQueryState('status'); 
 const [page,    setPage]    = useQueryState('page', 
   { defaultValue: '1' }); 
 ``` 
 
 ## Rules 
 
 - All filter state lives in URL — not in component state 
 - No hidden filter state 
 - Sharing a URL must reproduce the exact same view 
 - Back button must restore previous filter state 
 
 --- 
 
 # 27. Accessibility (A11y) 
 
 ## Rules 
 
 - All interactive elements must be keyboard accessible 
 - Use semantic HTML elements 
 - All form inputs MUST have labels 
 - All images MUST have alt text 
 - Color alone MUST NOT convey meaning (use icons + color) 
 - Focus states must be visible 
 
 ## ❌ Forbidden 
 
 ```tsx 
 <div onClick={handleDelete}>Delete</div> 
 <input placeholder="Search..." />  // no label 
 ``` 
 
 ## ✅ Required 
 
 ```tsx 
 <button onClick={handleDelete}>Delete</button> 
 <label htmlFor="search">Search</label> 
 <input id="search" placeholder="Search..." /> 
 ``` 
 
 --- 
 
 # 28. Performance Rules 
 
 ## Component Optimization 
 
 - Prefer RSC over client components when no interactivity 
 - Use `dynamic()` imports for heavy components 
 - Use `React.memo` for expensive list items 
 
 ```ts 
 const HeavyChart = dynamic( 
   () => import('@/components/admin/dashboard/RevenueChart'), 
   { loading: () => <CardSkeleton /> }, 
 ); 
 ``` 
 
 ## Rules 
 
 - No unnecessary `'use client'` directives 
 - Every `'use client'` must have a reason 
 - Avoid prop drilling more than 2 levels — use Zustand 
 - No anonymous functions in JSX for expensive renders 
 
 --- 
 
 # 29. RSC + TanStack Query Hydration Pattern 
 
 When a page uses RSC for initial data AND needs 
 client-side refetching: 
 
 ```tsx 
 // page.tsx (RSC) 
 export default async function UsersPage({ params }) { 
   const initialData = await getUsers(Number(params.store)); 
 
   return ( 
     <HydrationBoundary 
       state={dehydrate(getQueryClient())} 
     > 
       <UsersClient 
         storeId={Number(params.store)} 
         initialData={initialData} 
       /> 
     </HydrationBoundary> 
   ); 
 } 
 
 // UsersClient.tsx ('use client') 
 export function UsersClient({ storeId, initialData }) { 
   const { data } = useQuery({ 
     queryKey:    ['admin', 'users', storeId], 
     queryFn:     () => getUsers(storeId), 
     initialData: initialData, 
   }); 
 } 
 ``` 
 
 ## Rules 
 
 - RSC provides `initialData` to avoid loading flash 
 - TanStack Query handles revalidation after that 
 - This pattern is used for all paginated admin tables 
 
 --- 
 
 # 30. API Versioning 
 
 ## Rule 
 
 All API calls MUST use the `/api/v1/` prefix. 
 The version is controlled by the backend. 
 Never hardcode version strings in individual API functions. 
 
 ```ts 
 // src/lib/api/axios.ts 
 const api = axios.create({ 
   baseURL: `${process.env.NEXT_PUBLIC_API_URL}/api/v1`, 
 }); 
 
 // src/lib/api/admin/users.ts 
 // ✅ Correct — version handled by baseURL 
 export const getUsers = (storeId: number) => 
   api.get(`/admin/stores/${storeId}/users`); 
 
 // ❌ Forbidden — hardcoded version 
 export const getUsers = (storeId: number) => 
   api.get(`/api/v1/admin/stores/${storeId}/users`); 
 ``` 
 
 --- 
 
 # 31. Testing Strategy 
 
 ## Scope (Minimal — implement when ready) 
 
 | Type | Tool | Target | 
 |------|------|--------| 

 export default async function DashboardPage({ 
   params, 
 }: { 
   params: { store: string }; 
 }) { 
   const stats = await getStats(Number(params.store)); 
   return <DashboardClient initialStats={stats} />; 
 } 
 ``` 
 
 ## When to Use TanStack Query 
 
 Use TanStack Query when: 
 - Data needs to refetch after mutations 
 - Paginated tables 
 - Data changes based on user interaction 
 - Background refetching is needed 
 
 ```tsx 
 // Client component 
 const { data, isLoading } = useQuery({ 
   queryKey: ['admin', 'users', storeId, filters], 
   queryFn:  () => getUsers(storeId, filters), 
 }); 
 ``` 
 
 ## Rules 
 
 - RSC for page-level initial data 
 - TanStack Query for client interactions and mutations 
 - NEVER use useEffect + fetch for data fetching 
 - RSC uses server API layer for initial data 
 - TanStack Query uses client API layer for revalidation 
 - Pass RSC data as `initialData` to TanStack Query 
 - See Section 29 for the hydration pattern 
 - All TanStack Query hooks live in `src/lib/hooks/` 
 - Query keys MUST follow: `['domain', 'entity', storeId, ...filters]` 
 
 --- 
 
 # 7. Authentication 
 
 ## How It Works 
 
 1. User logs in → backend returns Sanctum token 
 2. Token stored in httpOnly cookie via Next.js API route 
 3. Every request sends cookie automatically (`withCredentials: true`) 
 4. `middleware.ts` protects all admin routes 
 5. On 401 → redirect to login 
 
 ## Zustand Auth Store 
 
 ```ts 
 // src/stores/authStore.ts 
 interface AuthStore { 

---

# 32. Internationalization (i18n)

## Why It Exists

The backend supports English and Arabic. The frontend MUST
match. Skipping this now means rewriting half the UI later.

## Tool

Use `next-intl` for App Router i18n support.

## Structure

```plaintext
src/
 ├── locales/
 │    ├── en/
 │    │    ├── common.json
 │    │    ├── users.json
 │    │    ├── products.json
 │    │    ├── orders.json
 │    │    ├── dashboard.json
 │    │    └── errors.json
 │    └── ar/
 │         ├── common.json
 │         ├── users.json
 │         ├── products.json
 │         ├── orders.json
 │         ├── dashboard.json
 │         └── errors.json
```
Usage
```react
import { useTranslations } from 'next-intl';

export function UsersPage() {
  const t = useTranslations('users');

  return <h1>{t('title')}</h1>;
}
```
Locale Files Example
```json
// locales/en/users.json
{
  "title":               "Users",
  "blocked_success":     "User has been blocked.",
  "unblocked_success":   "User has been unblocked.",
  "deleted_success":     "User has been deleted.",
  "restored_success":    "User has been restored.",
  "not_found":           "User not found."
}
```
```json
// locales/ar/users.json
{
  "title":               "المستخدمون",
  "blocked_success":     "تم حظر المستخدم.",
  "unblocked_success":   "تم رفع الحظر عن المستخدم.",
  "deleted_success":     "تم حذف المستخدم.",
  "restored_success":    "تم استعادة المستخدم.",
  "not_found":           "المستخدم غير موجود."
}
```
RTL Support
Arabic requires RTL layout direction.
Set `dir` attribute on `<html>` based on active locale.

```react
// app/layout.tsx
<html lang={locale} dir={locale === 'ar' ? 'rtl' : 'ltr'}>
```
Tailwind RTL classes are used for layout mirroring:

```react
// ✅ Required for RTL-aware layouts
<div className="ltr:ml-4 rtl:mr-4">
```
Rules
ALL user-facing strings MUST use `t()` — no exceptions
No hardcoded text inside any component
Locale files MUST match backend lang file structure
Toast messages MUST use `t()`
Error messages MUST use `t()`
RTL layout MUST work correctly for Arabic
❌ Forbidden
```react
toast.success('User blocked successfully.');
<h1>Users</h1>
```
✅ Required
```react
toast.success(t('users.blocked_success'));
<h1>{t('users.title')}</h1>
```
# 33. Logging & Debug Strategy
Logger Utility (`src/lib/logger.ts`)
```typescript
export const logger = {
  info: (...args: unknown[]) => {
    if (process.env.NODE_ENV === 'development') {
      console.log('[INFO]', ...args);
    }
  },
  warn: (...args: unknown[]) => {
    if (process.env.NODE_ENV === 'development') {
      console.warn('[WARN]', ...args);
    }
  },
  error: (...args: unknown[]) => {
    console.error('[ERROR]', ...args);
  },
};
```
Usage
```typescript
import { logger } from '@/lib/logger';

logger.info('Fetching users for store', storeId);
logger.error('Failed to block user', error);
```
Rules
`console.log` is FORBIDDEN in committed code
Use `logger.info()` for development debugging
Use `logger.error()` for real errors — these log in production
NEVER log: tokens, passwords, full user objects
NEVER log: API responses that contain sensitive data
Logger is the only allowed debug output
# 34. Feature Flags 
 
 > ⚠️ This section is superseded by Section 48. 
 > See Section 48 for the complete feature flag system. 
 > Section 48 covers both frontend flags and backend-driven flags. 
# 35. Component Size & Complexity Limits
Rules
Maximum 250 lines per component file
If a component exceeds 250 lines → split it
Business logic MUST be extracted to hooks
Components render UI only — no logic inside
How to Split
```plaintext
// Too big — split this:
UsersPage.tsx (400 lines)

// Into this:
UsersPage.tsx         ← composes only (~30 lines)
UsersTable.tsx        ← table UI
UsersFilters.tsx      ← filter bar
UserActionsMenu.tsx   ← row action dropdown
useUsersPage.ts       ← all page logic
```
Rules for Hooks
Hooks MUST be pure (no side effects outside React lifecycle)
- Hooks MAY call hooks from the same domain 
 - Hooks MAY call hooks from shared/core domains 
   (authStore, storeStore, uiStore) 
 - Hooks MUST NOT call hooks from a different feature domain 
   (users hook calling products hook directly is forbidden)
Hooks return a consistent shape:
```typescript
return {
  data,
  isLoading,
  isError,
  error,
  // mutations if applicable
  mutate,
  isPending,
};
```
Query hooks named: `useUsers`, `useProducts`
Mutation hooks named: `useCreateUser`, `useBlockUser`
# 36. Empty State Design System
Rule
Every list, table, or data view MUST have an empty state.
No blank areas. No silent nothing.

Shared Component
```react
// components/common/EmptyState.tsx

interface EmptyStateProps {
  title:        string;
  description?: string;
  action?:      React.ReactNode;
  icon?:        React.ReactNode;
}

export function EmptyState({
  title,
  description,
  action,
  icon,
}: EmptyStateProps) {
  return (
    <div className="flex flex-col items-center
                    justify-center py-16 text-center">
      {icon && (
        <div className="mb-4 text-muted">{icon}</div>
      )}
      <h3 className="text-fore font-medium">{title}</h3>
      {description && (
        <p className="mt-1 text-muted text-sm">{description}</p>
      )}
      {action && (
        <div className="mt-4">{action}</div>
      )}
    </div>
  );
}
```
Usage
```react
<EmptyState
  title={t('users.empty_title')}
  description={t('users.empty_description')}
  icon={<UsersIcon className="w-10 h-10" />}
  action={<CreateUserButton storeId={storeId} />}
/>
```
Rules
Use `EmptyState` component — never custom inline empties
Title is required — description is optional
Action is optional — only show if user can act
All text through `t()` — no hardcoded strings
# 37. Form UX Standards
Rules
Disable submit button while form is submitting
Show loading spinner inside submit button while submitting
Focus first invalid field on validation error
Reset form on success if it is a `create` form
Do not reset form on success if it is an `edit` form
Map API validation errors to form fields automatically
No double submissions — disable button immediately on click
Standard Submit Button Pattern
```react
<Button
  type="submit"
  disabled={isSubmitting || isPending}
>
  {isPending ? (
    <>
      <Spinner className="mr-2 h-4 w-4" />
      {t('common.saving')}
    </>
  ) : (
    t('common.save')
  )}
</Button>
```
API Error → Form Field Mapping
```typescript
onError: (error: ApiError) => {
  if (error.errors) {
    Object.entries(error.errors).forEach(([field, messages]) => {
      form.setError(field as keyof FormData, {
        message: messages[0],
      });
    });
  } else {
    toast.error(error.message);
  }
},
```
Rules
Validation errors from API MUST appear on the correct field
Generic errors (non-field) MUST appear as toast
Never show raw error codes to users
# 38. Layout & Scroll Behavior
Layout Structure
```text
┌─────────────────────────────────────────┐
│  Sidebar (fixed, full height)           │
│  ┌───────────────────────────────────┐  │
│  │  Topbar (fixed, content width)    │  │
│  ├───────────────────────────────────┤  │
│  │  Content area (scrolls)           │  │
│  │                                   │  │
│  │                                   │  │
│  └───────────────────────────────────┘  │
└─────────────────────────────────────────┘
```
Rules
Sidebar is fixed — never scrolls
Topbar is fixed — never scrolls
Content area scrolls independently
Tables that overflow horizontally MUST have horizontal scroll
No body scroll lock side effects from modals
No layout shift when modal opens (scrollbar compensation)
Sidebar collapses on mobile — never overlaps content
Tailwind Layout Classes
```react
// Root layout structure
<div className="flex h-screen overflow-hidden bg-bg">
  <Sidebar />                              // fixed sidebar
  <div className="flex flex-col flex-1 overflow-hidden">
    <Topbar />                             // fixed topbar
    <main className="flex-1 overflow-y-auto p-6">
      {children}                           // scrollable content
    </main>
  </div>
</div>
```
# 39. Date & Time Handling
Rules
All dates come from backend in ISO 8601 format
All date formatting via shared utility — never inline
Never use `new Date().toLocaleString()` directly in components
Timezone: display in user's local timezone by default
Date Utility (`src/lib/utils/date.ts`)
```typescript
import { format, formatDistanceToNow, parseISO } from 'date-fns';
import { ar, enUS } from 'date-fns/locale';

const localeMap = {
  en: enUS,
  ar: ar,
};

export function formatDate(
  isoString: string,
  locale: string = 'en',
): string {
  return format(
    parseISO(isoString),
    'MMM dd, yyyy',
    { locale: localeMap[locale] ?? enUS },
  );
}

export function formatDateTime(
  isoString: string,
  locale: string = 'en',
): string {
  return format(
    parseISO(isoString),
    'MMM dd, yyyy — HH:mm',
    { locale: localeMap[locale] ?? enUS },
  );
}

export function timeAgo(
  isoString: string,
  locale: string = 'en',
): string {
  return formatDistanceToNow(parseISO(isoString), {
    addSuffix: true,
    locale: localeMap[locale] ?? enUS,
  });
}
```
Usage
```react
// ✅ Required
import { formatDate } from '@/lib/utils/date';
<span>{formatDate(user.created_at, locale)}</span>

// ❌ Forbidden
<span>{new Date(user.created_at).toLocaleDateString()}</span>
```
# 40. File Upload Strategy
Rules
Use `multipart/form-data` for all file uploads
Validate file size and type on client before upload
Show upload progress for files over 1MB
Show image preview before upload for image fields
Show success and error feedback via toast
Accepted types and max sizes defined in config
Upload Config (`src/config/app.ts`)
```typescript
export const UPLOAD_CONFIG = {
  maxImageSize:     5 * 1024 * 1024,   // 5MB
  maxDocumentSize: 10 * 1024 * 1024,   // 10MB
  acceptedImageTypes: [
    'image/jpeg',
    'image/png',
    'image/webp',
  ],
} as const;
```
Client Validation
```typescript
function validateFile(file: File): string | null {
  if (file.size > UPLOAD_CONFIG.maxImageSize) {
    return t('upload.too_large');
  }
  if (!UPLOAD_CONFIG.acceptedImageTypes.includes(file.type)) {
    return t('upload.invalid_type');
  }
  return null;
}
```
Axios Upload with Progress
```typescript
export const uploadMedia = async (
  storeId: number,
  file: File,
  onProgress?: (percent: number) => void,
) => {
  const form = new FormData();
  form.append('file', file);

  const { data } = await api.post(
    `/admin/stores/${storeId}/media`,
    form,
    {
      headers: { 'Content-Type': 'multipart/form-data' },
      onUploadProgress: (e) => {
        if (e.total) {
          onProgress?.(Math.round((e.loaded * 100) / e.total));
        }
      },
    },
  );

  return data;
};
```
# 41. Frontend Security Rules
Rules
Never trust user-generated content — always sanitize before render
Never use `dangerouslySetInnerHTML` without sanitization
CSRF is handled automatically by Sanctum httpOnly cookies
Never expose auth tokens in:
URL parameters
localStorage
sessionStorage
Component state
Log output
Never expose internal store IDs in client-visible errors
Content Security Policy (CSP) headers set by Next.js config
Safe HTML Rendering (if needed)
```typescript
import DOMPurify from 'dompurify';

// Only when rendering user-generated HTML content
const clean = DOMPurify.sanitize(userContent);
<div dangerouslySetInnerHTML={{ __html: clean }} />
```
Rules
`dangerouslySetInnerHTML` requires DOMPurify — no exceptions
Sensitive data never logged (see Section 33)
API errors never expose stack traces to UI
# 42. Build & Code Quality Rules
TypeScript
Strict mode ON — zero tolerance for errors
`any` type is FORBIDDEN
All build must pass `tsc --noEmit` with zero errors
ESLint
Required rules (`.eslintrc`):

```json
{
  "rules": {
    "no-console":               "error",
    "@typescript-eslint/no-explicit-any": "error",
    "react-hooks/rules-of-hooks": "error",
    "react-hooks/exhaustive-deps": "warn"
  }
}
```
Prettier
Enforced formatting. No debates on style.

```json
{
 | Unit | Vitest | Utils, helpers, store logic | 
 | Component | React Testing Library | Common components | 
 | E2E | Playwright | Critical user flows | 
 
 ## Critical Flows to Test (E2E) 
 
 - Login and redirect to dashboard 
 - View users list with pagination 
 - Block and unblock a user 
 - Create a product 
 - Update order status 
 
 ## Rules 
 
 - Utils MUST be unit tested 
 - No tests required for page components yet 
 - E2E tests cover happy paths only for now 
 - Test files live next to the file they test 
 
 --- 
 
 # 44. Error Normalizer 
 
 ## Problem 
 
 Axios errors are inconsistent. Different errors have different 
 shapes. Hooks and components must never handle raw Axios errors. 
 
 ## Normalizer (`src/lib/api/client/error.ts`) 
 
 ```ts 
 import axios from 'axios'; 
 import type { ApiError } from '@/types/api'; 
 
 export function normalizeError(error: unknown): ApiError { 
   if (axios.isAxiosError(error) && error.response?.data) { 
     return error.response.data as ApiError; 
   } 
 
   if (axios.isAxiosError(error) && !error.response) { 
     return { 
       status:     false, 
       message:    'Network error. Please check your connection.', 
       error_code: 'NETWORK_ERROR', 
       errors:     null, 
     }; 
   } 
 
   return { 
     status:     false, 
     message:    'An unexpected error occurred.', 
     error_code: 'UNKNOWN_ERROR', 
     errors:     null, 
   }; 
 } 
 ``` 
 
 ## Usage in Axios Interceptor 
 
 ```ts 
 api.interceptors.response.use( 
   (response) => response, 
   (error) => { 
     const normalized = normalizeError(error); 
 
     if (normalized.error_code === 'AUTH_002') { 
       // redirect to login 
     } 
 
     return Promise.reject(normalized); 
   }, 
 ); 
 ``` 
 
 ## Rules 
 
 - ALL API errors MUST pass through `normalizeError()` 
 - Hooks receive `ApiError` — never raw Axios errors 
 - Components receive `ApiError` from hooks — never raw errors 
 - `normalizeError` is called in the Axios interceptor only 
 
 --- 
 
 # 45. Query Key Factory 
 
 ## Problem 
 
 Inline query keys like `['admin', 'users', storeId]` are 
 fragile. Typos cause silent bugs. Invalidation breaks. 
 
 ## Factory (`src/lib/queryKeys.ts`) 
 
 ```ts 
 export const queryKeys = { 
   // Users 
   users: (storeId: number, filters?: unknown) => 
     ['admin', 'users', storeId, filters] as const, 
 
   user: (storeId: number, userId: number) => 
     ['admin', 'users', storeId, userId] as const, 
 
   // Products 
   products: (storeId: number, filters?: unknown) => 
     ['admin', 'products', storeId, filters] as const, 
 
   product: (storeId: number, productId: number) => 
     ['admin', 'products', storeId, productId] as const, 
 
   // Orders 
   orders: (storeId: number, filters?: unknown) => 
     ['admin', 'orders', storeId, filters] as const, 
 
   order: (storeId: number, orderId: number) => 
     ['admin', 'orders', storeId, orderId] as const, 
 
   // Dashboard 
   dashboardStats: (storeId: number) => 
     ['admin', 'dashboard', 'stats', storeId] as const, 
 
   dashboardRecentOrders: (storeId: number) => 
     ['admin', 'dashboard', 'recent-orders', storeId] as const, 
 
   dashboardTopProducts: (storeId: number) => 
     ['admin', 'dashboard', 'top-products', storeId] as const, 
 } as const; 
 ``` 
 
 ## Usage 
 
 ```ts 
 // ✅ Required 
 const { data } = useQuery({ 
   queryKey: queryKeys.users(storeId, filters), 
   queryFn:  () => getUsers(storeId, filters), 
 }); 
 
 // Invalidation 
 queryClient.invalidateQueries({ 
   queryKey: queryKeys.users(storeId), 
 }); 
 
 // ❌ Forbidden 
 queryKey: ['admin', 'users', storeId, filters] 
 ``` 
 
 ## Rules 
 
 - Query keys MUST be created via `queryKeys` factory 
 - Never inline query keys anywhere 
 - Invalidation MUST use the same factory 
 
 --- 
 
 # 46. Cross-Layer Import Rules 
 
 ## Import Direction (STRICT) 
 
 ``` 
 pages 
   → components 
     → hooks 
       → API layer 
         → types 
 
 Each layer may only import from layers below it. 
 Reverse imports are forbidden. 
 ``` 
 
 ## Rules 
 
 | Layer | May Import | 
 |-------|-----------| 
 | `app/` pages | `components/`, `hooks/`, `stores/`, `types/`, `config/` | 
 | `components/` | `hooks/`, `lib/utils/`, `types/`, `config/`, `components/ui/` | 
 | `lib/hooks/` | `lib/api/client/`, `lib/utils/`, `types/`, `config/` | 
 | `lib/api/client/` | `types/`, `config/` | 
 | `lib/api/server/` | `types/`, `config/` | 
 | `stores/` | `types/`, `config/` | 
 | `types/` | nothing | 
 | `config/` | `types/` only | 
 
 ## ❌ Forbidden 
 
 ```ts 
 // Component importing API directly 
 import { getUsers } from '@/lib/api/client/admin/users'; 
 
 // Hook importing a component 
 import { UserCard } from '@/components/admin/users/UserCard'; 
 
 // API importing a store 
 import { useAuthStore } from '@/stores/authStore'; 
 ``` 
 
 ## ✅ Required 
 
 ```ts 
 // Component uses hook — hook uses API 
 import { useUsers } from '@/lib/hooks/admin/useUsers'; 
 ``` 
 
 --- 
 
 # 47. DTO Mapping Layer 
 
 ## Problem 
 
 If UI depends directly on backend response shape, 
 any backend change breaks the entire UI. 
 
 ## Solution 
 
 Map API responses to frontend types before they reach UI. 
 
 ## Location 
 
 ```plaintext 
 src/lib/mappers/ 
  ├── user.mapper.ts 
  ├── product.mapper.ts 
  ├── order.mapper.ts 
  └── dashboard.mapper.ts 
 ``` 
 
 ## Example 
 
 ```ts 
 // src/lib/mappers/user.mapper.ts 
 
 import type { AdminUserApi }  from '@/types/api/user'; 
 import type { AdminUser }     from '@/types/user'; 
 
 export function mapUser(raw: AdminUserApi): AdminUser { 
   return { 
     id:        raw.id, 
     name:      raw.name, 
     email:     raw.email, 
     isActive:  raw.is_active, 
     createdAt: raw.created_at, 
     role:      raw.store_role ?? 'customer', 
   }; 
 } 
 
 export function mapUsers(raw: AdminUserApi[]): AdminUser[] { 
   return raw.map(mapUser); 
 } 
 ``` 
 
 ## Usage in API Layer 
 
 ```ts 
 // src/lib/api/client/admin/users.ts 
 import { mapUsers } from '@/lib/mappers/user.mapper'; 
 
 export const getUsers = async ( 
   storeId: number, 
 ): Promise<AdminUser[]> => { 
   const { data } = await api.get( 
     `/admin/stores/${storeId}/users`, 
   ); 
   return mapUsers(data.data); 
 }; 
 ``` 
 
 ## Rules 
 
 - API responses MUST be mapped before reaching hooks or UI 
 - UI types live in `src/types/` 
 - Raw API types live in `src/types/api/` 
 - Mappers live in `src/lib/mappers/` 
 - UI MUST NOT depend on raw backend field names 
 
 --- 
 
 # 48. Feature Flags (Updated) 
 
 ## Two Types of Flags 
 
 | Type | Source | Purpose | 
 |------|--------|---------| 
 | Frontend flags | `src/config/features.ts` | UI-only, temporary toggles | 
 | Backend flags | User object from API | Critical feature gates | 
 
 ## Frontend Flags (`src/config/features.ts`) 
 
 ```ts 
 export const FEATURES = { 
   NEW_DASHBOARD_LAYOUT: false, 
   PRODUCT_BULK_UPLOAD:  false, 
   ORDER_EXPORT_CSV:     false, 
 } as const; 
 ``` 
 
 ## Backend Flags (from Auth Store) 
 
 ```ts 
 interface AuthStore { 
   features: string[]; 
   hasFeature: (flag: string) => boolean; 
 } 
 
 // Inside store 
 hasFeature: (flag: string) => { 
   return get().features.includes(flag); 
 } 
 ``` 
 
 ## Usage 
 
 ```tsx 
 // Frontend flag — UI only 
 {FEATURES.NEW_DASHBOARD_LAYOUT && <NewLayout />} 
 
 // Backend flag — critical gates 
 const { hasFeature } = useAuthStore(); 
 {hasFeature('analytics_charts') && <RevenueChart />} 
 ``` 
 
 ## Rules 
 
 - Critical feature gates MUST come from backend 
 - Frontend flags are for UI-only or temporary toggles only 
 - Frontend flags MUST NOT gate security-sensitive features 
 - Remove frontend flags once feature is stable and released 
 
 --- 
 
 # 49. Suspense & Streaming Rules 
 
 ## Rules 
 
 - Use `Suspense` boundaries for slow RSC sections 
 - Never block the entire page for one slow request 
 - Each independent section gets its own `Suspense` 
 
 ## Pattern 
 
 ```tsx 
 // app/(admin)/stores/[store]/dashboard/page.tsx 
 
 export default function DashboardPage() { 
   return ( 
     <div> 
       <PageHeader title={t('dashboard.title')} /> 
 
       <Suspense fallback={<StatsSkeleton />}> 
         <StatsSection storeId={storeId} /> 
       </Suspense> 
 
       <Suspense fallback={<TableSkeleton />}> 
         <RecentOrdersSection storeId={storeId} /> 
       </Suspense> 
 
       <Suspense fallback={<TableSkeleton />}> 
         <TopProductsSection storeId={storeId} /> 
       </Suspense> 
     </div> 
   ); 
 } 
 ``` 
 
 ## Rules 
 
 - Each Suspense boundary has a skeleton fallback 
 - Skeletons match the shape of the content they replace 
 - Never use a generic spinner as a Suspense fallback 
 - Group related data under one Suspense boundary 
 
 --- 
 
 # 50. Barrel File Rules (index.ts) 
 
 ## Rules 
 
 - `index.ts` is allowed ONLY as a public API for a folder 
 - Deep barrel chains are FORBIDDEN 
 - Never re-export everything blindly 
 
 ## ✅ Allowed 
 
 ```ts 
 // components/common/index.ts 
 export { DataTable }   from './DataTable/DataTable'; 
 export { EmptyState }  from './EmptyState'; 
 export { PageHeader }  from './PageHeader'; 
 ``` 
 
 ## ❌ Forbidden 
 
 ```ts 
 // Barrel that re-exports barrels 
 export * from './DataTable'; 
 export * from './EmptyState'; 
 export * from './PageHeader'; 
 ``` 
 
 ## ❌ Also Forbidden 
 
 ```ts 
 // Deep import chain 
 import { x } from '@/components/common/DataTable/index'; 
 // Then DataTable/index re-exports from DataTable/columns/index 
 // Then that re-exports from DataTable/columns/helpers/index 
 ``` 
 
 --- 
 
 # 56. QueryClient Retry Strategy 
 
 ## Problem 
 
 Without a smart retry strategy, all failures behave the same. 
 Network errors should retry. Validation errors should never retry. 
 Retrying a 422 error wastes requests and confuses users. 
 
 ## QueryClient Config (`src/lib/queryClient.ts`) 
 
 ```ts 
 import { QueryClient } from '@tanstack/react-query'; 
 import { QUERY_CONFIG } from '@/config/query'; 
 import type { ApiError } from '@/types/api'; 
 
 function shouldRetry( 
   failureCount: number, 
   error: unknown, 
 ): boolean { 
   const apiError = error as ApiError; 
 
   // Never retry validation errors 
   if (apiError?.error_code === 'VAL_001') return false; 
 
   // Never retry auth errors 
   if (apiError?.error_code?.startsWith('AUTH_')) return false; 
 
   // Never retry not found errors 
   if (apiError?.error_code === 'SYS_002') return false; 
 
   // Retry up to 2 times for network/server errors 
   return failureCount < 2; 
 } 
 
 export const queryClient = new QueryClient({ 
   defaultOptions: { 
     queries: { 
       staleTime:           QUERY_CONFIG.staleTime, 
       refetchOnWindowFocus: QUERY_CONFIG.refetchOnWindowFocus, 
       retry:               shouldRetry, 
     }, 
     mutations: { 
       retry: false,  // Never retry mutations automatically 
     }, 
   }, 
 }); 
 ``` 
 
 ## Rules 
 
 - Network errors retry up to 2 times automatically 
 - Validation errors (VAL_001) NEVER retry 
 - Auth errors (AUTH_*) NEVER retry — redirect instead 
 - Not found errors (SYS_002) NEVER retry 
 - Mutations NEVER retry automatically 
 - The queryClient instance is created once and shared 
 
 --- 
 
 # 57. i18n Key Consistency Rules 
 
 ## Problem 
 
 If en/users.json has a key that ar/users.json is missing, 
 the Arabic UI breaks silently. No error. No warning. 
 Users see a raw key string like users.blocked_success. 
 
 ## Rules 
 
 - Every key in en/ MUST exist in ar/ with the same path 
 - Every key in ar/ MUST exist in en/ with the same path 
 - Key names MUST follow dot notation consistently 
 - Nested objects MUST have the same depth in both locales 
 - New translation keys MUST be added to BOTH locales 
   in the same commit — never one without the other 
 
 ## Key Naming Convention 
 
 ```text 
 domain.action_context 
 
 users.title 
 users.blocked_success 
 users.empty_title 
 users.empty_description 
 products.created_success 
 orders.status_updated 
 errors.generic 
 errors.network 
 common.save 
 common.cancel 
 common.saving 
 common.loading 
 ``` 
 
 ## File Consistency Check 
 
 Both locale files for the same domain MUST be identical 
 in structure — only the values differ: 
 
 ```json 
 // en/users.json 
 { 
   "title":             "Users", 
   "blocked_success":   "User has been blocked.", 
   "empty_title":       "No users found", 
   "empty_description": "Try adjusting your filters" 
 } 
 
 // ar/users.json — MUST have same keys 
 { 
   "title":             "المستخدمون", 
   "blocked_success":   "تم حظر المستخدم.", 
   "empty_title":       "لا يوجد مستخدمون", 
   "empty_description": "حاول تعديل الفلاتر" 
 } 
 ``` 
 
 ## CI Enforcement (Recommended) 
 
 Add a script that compares key sets between en/ and ar/: 
 
 ```text 
 scripts/check-translations.ts 
 ``` 
 
 Fails the build if any key is missing in either locale. 
 
 --- 
 
 # 58. Search & Filter Debounce Rule 
 
 ## Problem 
 
 Without debouncing, every keystroke in a search field 
 fires an API request. A user typing "john" sends 4 requests. 
 This spams the backend and creates race conditions. 
 
 ## Rule 
 
 All search inputs and filter changes that trigger API calls 
 MUST be debounced by 300ms minimum. 
 
 ## Implementation 
 
 ```ts 
 // src/lib/hooks/useDebounce.ts 
 import { useState, useEffect } from 'react'; 
 
 export function useDebounce<T>(value: T, delay: number = 300): T { 
   const [debouncedValue, setDebouncedValue] = useState<T>(value); 
 
   useEffect(() => { 
     const timer = setTimeout(() => { 
       setDebouncedValue(value); 
     }, delay); 
 
     return () => clearTimeout(timer); 
   }, [value, delay]); 
 
   return debouncedValue; 
 } 
 ``` 
 
 ## Usage 
 
 ```ts 
 // In filter hook 
 export function useUsersFilters() { 
   const [filters, setFilters] = useQueryStates({ 
     search:   parseAsString.withDefault(''), 
     page:     parseAsInteger.withDefault(1), 
     per_page: parseAsInteger.withDefault(15), 
   }); 
 
   const debouncedSearch = useDebounce(filters.search, 300); 
 
   // Pass debouncedSearch to the query — not raw search 
   const query = useUsers(storeId, { 
     ...filters, 
     search: debouncedSearch, 
   }); 
 
   return { filters, setFilters, query }; 
 } 
 ``` 
 
 ## Rules 
 
 - Search inputs MUST debounce by 300ms minimum 
 - Filter dropdowns (select, enum) do NOT need debounce 
   — they fire once on selection 
 - Pagination changes do NOT need debounce 
   — they fire once on click 
 - The debounce value is passed to the query 
   — the raw input value updates the URL immediately 
 - useDebounce lives in src/lib/hooks/useDebounce.ts 
 - Never implement debounce inline — always use the shared hook 
 
 --- 
 
 # Final Hard Rules (Definitive & Complete) 
 
 This section replaces all previous "Final Hard Rules" sections. 
 
 ```text 
 NO HARDCODED COLORS          — token classes only. 
 NO HARDCODED TEXT            — t() translations only. 
 NO HARDCODED TOAST TEXT      — t() translations only. 
 NO HARDCODED URLS            — ROUTES config only. 
 NO MAGIC STRINGS             — config constants only. 
 NO ANY TYPE                  — TypeScript strict always. 
 NO LOCALSTORAGE FOR AUTH     — httpOnly cookies only. 
 NO MANUAL COOKIE READING     — withCredentials handles client. 
 NO CREDENTIALS INCLUDE RSC   — forward cookies() manually. 
 NO AXIOS IN RSC              — server API layer only. 
 NO FETCH IN CLIENT           — Axios client layer only. 
 NO RAW AXIOS ERRORS          — normalizeError() always. 
 NO INLINE QUERY KEYS         — queryKeys factory always. 
 NO FAT PAGES                 — pages compose only. 
 NO FAT COMPONENTS            — max 250 lines, split if over. 
 NO USEEFFECT FOR DATA       — RSC or TanStack Query only. 
 NO API CALLS IN COMPONENTS   — hooks only. 
 NO REVERSE LAYER IMPORTS     — follow import direction. 
 NO ALERT()                   — toast only. 
 NO CONSOLE.LOG               — logger utility only. 
 NO DIV BUTTONS               — semantic HTML only. 
 NO HIDDEN FILTER STATE       — sync with URL always. 
 NO UNTYPED QUERY PARAMS      — Zod filter schemas always. 
 NO UNVALIDATED UPLOADS       — client validate before send. 
 NO UNSANITIZED HTML          — DOMPurify before render. 
 NO CLIENT COMPONENT          — without a documented reason. 
 NO DOUBLE SUBMISSIONS        — disable on first click. 
 NO INLINE DATE FORMATTING    — date utility only. 
 NO DEEP BARREL CHAINS        — index.ts for public API only. 
 NO RAW API TYPES IN UI       — mappers always. 
 NO FRONTEND FLAG FOR SECURITY — backend flags for gates. 
 NO NAVIGATION IN HOOKS       — component layer only. 
 NO TOAST IN HOOKS            — component layer only. 
 NO DERIVED STATE IN STATE    — derive or useMemo only. 
 NO ZUSTAND FOR STOREID API   — URL params are source of truth. 
 NO REQUEST WITHOUT TIMEOUT   — 10s timeout always. 
 NO MUTATION AUTO-RETRY       — mutations never retry. 
 NO UNMATCHED TRANSLATION KEYS — en and ar must be identical. 
 NO UNDEBOUNCED SEARCH        — 300ms debounce always. 
 NO CROSS-DOMAIN HOOK CALLS   — same domain or shared only. 
 ``` 
