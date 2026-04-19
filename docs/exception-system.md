# Exception Handling System Documentation

## 1. Quick Start (TL;DR)

For developers and AI agents, follow these core principles:

-   **Success**: Always use `ApiResponse::success()` for successful responses.
-   **Errors**: Throw a specific, custom exception for **all** errors.
-   **No Manual JSON**: Never use `response()->json()` to create a response.
-   **Avoid `try/catch`**: Only use `try/catch` for specific, approved cases like wrapping third-party exceptions.
-   **Extend `BaseApiException`**: All custom exceptions MUST extend `BaseApiException`.

#### ✅ Correct Usage Example:
```php
if (!$user) {
    // Throw an exception for the error case.
    // The global handler will format the final JSON response.
    throw new InvalidCredentialsException();
}

// Return a success response using the ApiResponse helper.
return ApiResponse::success($user);
```

---

## 2. File Structure

The exception handling system is organized across the following key files and directories. Adhere to this structure when adding new exceptions.

```
app/
├── Enums/
│   └── ErrorCode.php             # Central enum for all machine-readable error codes.
├── Exceptions/
│   ├── Auth/                     # Exceptions related to authentication & authorization.
│   ├── Order/                    # Exceptions related to order processing.
│   ├── Payment/                  # Exceptions related to billing and payments.
│   │
│   ├── BaseApiException.php      # The base class all custom exceptions MUST extend.
│   ├── ExceptionRegistrar.php    # The global exception handler.
│   └── NotFoundException.php     # Generic "Not Found" exception.
│
└── Support/
    └── ApiResponse.php           # Helper for generating SUCCESS responses only.
```

---

## 3. Overview

This document outlines the standardized exception handling and API response system for this Laravel project.

The primary goal of this system is to ensure that all API responses, both for success and error scenarios, are consistent, predictable, and easy for clients (including frontend applications and third-party services) to consume.

It solves two common problems in API development:
1.  **Inconsistent Response Structures**: Without a standard, different endpoints might return errors in different formats, complicating client-side handling.
2.  **Messy Controller Logic**: Mixing business logic with response formatting and `try/catch` blocks makes controllers difficult to read, maintain, and test.

This system centralizes error handling and enforces a uniform response structure across the entire application.

---

## 4. Response Format Standard

All API endpoints MUST adhere to the following JSON response structures.

### Success Response

Used when an operation completes successfully.

-   **`status`**: `true`
-   **`message`**: A human-readable success message.
-   **`data`**: The payload of the response. Can be an object, array, or `null`.

```json
{
  "status": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": 1,
      "name": "Alex Doe",
      "email": "alex@example.com"
    },
    "token": "1|aBcDeFgHiJkLmNoPqRsTuVwXyZ"
  }
}
```

### Paginated Success Response

For endpoints that return a paginated list of resources, the structure includes a `meta` key for pagination details. The `data` key contains only the array of resources, keyed by the resource name (e.g., `products`).

```json
{
  "status": true,
  "message": "Success",
  "data": {
    "products": [...]
  },
  "meta": {
    "pagination": {
      "total": 100,
      "count": 20,
      "per_page": 20,
      "current_page": 1,
      "total_pages": 5
    },
    "filters": {
        "descendants": true,
        "min_price": 100,
        "max_price": 5000
    }
  }
}
```

-   **`data`**: Contains only the resource data under a key (e.g., `products`).
-   **`meta.pagination`**: Contains all pagination information derived directly from the Laravel Paginator instance.
-   **`meta`**: Can include other relevant metadata, such as the filters applied to the query.

### Error Response

Used for all anticipated and unexpected errors.

-   **`status`**: `false`
-   **`message`**: A human-readable error message.
-   **`error_code`**: A unique, stable string identifying the specific error.
-   **`errors`**: An object containing detailed validation errors, typically only present for `422 Unprocessable Entity` responses. For all other errors, this will be `null`.

```json
{
  "status": false,
  "message": "Invalid credentials provided.",
  "error_code": "AUTH_001",
  "errors": null
}
```

---

## 5. Error Types

Errors are classified into three main categories.

### 1. Validation Errors (`422`)

-   **What**: Errors that occur when incoming request data fails validation rules (e.g., a required field is missing, an email is invalid).
-   **How**: Handled automatically by Laravel's Form Requests. When validation fails, Laravel throws a `ValidationException`, which our system automatically formats into the standard error response with a `422` status code.
-   **Response**: The `errors` field will contain a detailed breakdown of which fields failed and why.

```json
{
  "status": false,
  "message": "The given data was invalid.",
  "error_code": "VAL_001",
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password must be at least 8 characters."]
  }
}
```

### 2. Domain Exceptions (Business Logic Errors)

-   **What**: Predictable errors that occur during the execution of business logic. These are anticipated failure scenarios.
-   **Examples**:
    -   Attempting to log in with an incorrect password (`InvalidCredentialsException`).
    -   Trying to purchase an out-of-stock product (`OutOfStockException`).
    -   Canceling an order that is not in a cancelable state (`OrderCancellationException`).
-   **How**: Implemented as custom exception classes that extend `BaseApiException`. These are thrown from services or controllers when a specific business rule is violated.

### 3. System Exceptions (Unexpected Errors)

-   **What**: Unhandled or unexpected errors, such as a database connection failure, a bug in the code, or a service being unavailable.
-   **How**: These are caught by the global exception handler (`ExceptionRegistrar`).
-   **Response**:
    -   In a **production** environment, a generic `500 Internal Server Error` response is returned with a non-specific message to avoid leaking sensitive information.
    -   In a **development** environment, the response will include the original exception message and stack trace for easier debugging.

---

## 6. `BaseApiException`

`BaseApiException` is the foundation of our custom exception system. **All custom domain exceptions MUST extend this class.**

-   **File**: `app/Exceptions/BaseApiException.php`
-   **Purpose**: It enforces a standard structure for all custom exceptions, ensuring they carry the necessary information to generate a consistent API error response.

#### Key Properties

-   `$statusCode`: The HTTP status code for the response (e.g., `401`, `404`, `409`).
-   `$errorCode`: The unique error code string from the `ErrorCode` enum.
-   `$message`: The default error message.
-   `$errors`: Optional array for detailed error messages (rarely used outside of validation).

By extending this class, we guarantee that our global exception handler can correctly format any custom exception into the standard error response.

---

## 7. Frontend Usage of `error_code`

The `error_code` field is the most critical part of an error response for client applications.

-   **Frontend MUST rely on `error_code`, not `message`.**
-   The `error_code` is a stable, machine-readable identifier that is guaranteed not to change.
-   The `message` is for human-readability and may be updated or translated in the future.

#### ✅ Correct Frontend Logic:
```javascript
try {
  await api.login(email, password);
} catch (error) {
  // Rely on the stable error_code for business logic.
  if (error.response.data.error_code === 'AUTH_001') {
    // Handle invalid credentials error
    setFormError('Invalid email or password.');
  } else {
    // Handle other errors
    showGenericErrorToast();
  }
}
```

---

## 8. Error Codes (`ErrorCode` Enum)

-   **File**: `app/Enums/ErrorCode.php`
-   **Purpose**: To provide stable, machine-readable identifiers for every possible domain error. This is crucial for frontend clients, allowing them to build reliable logic based on a code that will not change, even if the human-readable message does.

#### Naming Convention

Error codes are grouped by domain using a `DOMAIN_XXX` format. This is the single source of truth for all error codes.

-   `AUTH_XXX`: Authentication & Authorization
-   `ORD_XXX`: Order Processing
-   `PMT_XXX`: Payment & Billing
-   `SYS_XXX`: General System Errors
-   `VAL_XXX`: Validation

---

## 9. How to Create a New Exception

Follow these steps to create a new, compliant domain exception.

#### Step 1: Add Error Code to Enum

Add a new case to `app/Enums/ErrorCode.php`, following the established naming convention.

```php
// app/Enums/ErrorCode.php
enum ErrorCode: string
{
    // ...
    case ORD_003 = 'ORD_003'; // Reorder failed
}
```

#### Step 2: Create the Exception Class

Create a new class in the appropriate domain folder (e.g., `app/Exceptions/Order/`). It **MUST** extend `BaseApiException`.

#### Step 3: Define Class Properties and Constructor

Define the `statusCode` and `errorCode` as `protected` class properties. The constructor should only accept an optional `message` to override the default.

#### ✅ Correct Implementation (`ReorderFailedException`):

```php
// app/Exceptions/Order/ReorderFailedException.php
<?php

namespace App\Exceptions\Order;

use App\Enums\ErrorCode;
use App\Exceptions\BaseApiException;

class ReorderFailedException extends BaseApiException
{
    // 1. Define HTTP status code as a class property.
    protected int $statusCode = 422;

    // 2. Define the unique error code as a class property.
    protected string $errorCode = ErrorCode::ORD_003->value;

    // 3. The constructor only sets the human-readable message.
    public function __construct(string $message = 'Failed to reorder the items.')
    {
        parent::__construct($message);
    }
}
```

---

## 10. Existing Exception Examples

The project already contains several exceptions. Use them as a reference.

-   **Auth**:
    -   `InvalidCredentialsException`
    -   `UnauthorizedException`
    -   `TooManyRequestsException`
-   **Order**:
    -   `OutOfStockException`
    -   `OrderCancellationException`
-   **Payment**:
    -   `PaymentFailedException`
    -   `StripeServiceException`
-   **System**:
    -   `NotFoundException`

---

## 11. `ExceptionRegistrar`

-   **File**: `app/Exceptions/ExceptionRegistrar.php`
-   **Purpose**: This class acts as the application's global exception handler. It is registered in `app/Exceptions/Handler.php` and is responsible for catching all throwable exceptions and converting them into our standard API response format.

#### It Handles:

1.  `BaseApiException`: Formats it into a JSON response using the properties defined in the exception.
2.  `ValidationException`: Formats it into a `422` response with the `VAL_001` code and detailed `errors` object.
3.  `HttpException` (e.g., `NotFoundHttpException`): Converts it into a standard response (e.g., a `404` with `SYS_002` error code).
4.  `Throwable` (any other exception): Catches all other unexpected errors and generates a generic `500` server error response.

It also contains logic for logging errors, ensuring that critical system failures are recorded without exposing details to the end-user.

---

## 12. `ApiResponse` Helper

-   **File**: `app/Support/ApiResponse.php`
-   **Purpose**: A simple helper class for generating **success responses only**.

The `ApiResponse` helper is a final class that provides static methods for generating standard API responses. It is the **only** approved way to generate a success response.

-   `ApiResponse::success()`: For standard, non-paginated success responses.
-   `ApiResponse::paginated()`: For paginated responses.

#### `ApiResponse::paginated()`

This method is used exclusively for endpoints that return paginated data. It is designed to enforce the separation of resource data from pagination metadata.

It accepts the Laravel `Paginator` instance, the transformed resource data, and any additional metadata separately. This ensures that the pagination details are always sourced directly from the original paginator, preventing inconsistencies.

##### ✅ Correct Usage Example:
```php
$paginator = $query->paginate($perPage);

return ApiResponse::paginated(
    paginator: $paginator,
    data: ProductCardResource::collection($paginator->items()),
    additionalMeta: [
        'filters' => [
            'descendants' => $descendants,
            'min_price' => $variantStatus->min_price,
            'max_price' => $variantStatus->max_price,
            'earliest_manufacture' => $variantStatus->earliest_manufacture,
            'latest_expiry' => $variantStatus->latest_expiry
        ]
    ]
);
```

##### Design Decision: Why Pass the Paginator Separately?

We pass the `$paginator` instance directly to `ApiResponse::paginated()` to ensure the pagination metadata is sourced from the single source of truth.

**DO NOT** rely on a `ResourceCollection` to provide pagination data. While Laravel's `ResourceCollection` can add a `meta` key, this approach is less explicit and can lead to developers accidentally omitting pagination data or sourcing it incorrectly. Our approach makes the dependency on the paginator clear and guarantees consistency.

#### Usage

```php
use App\Support\ApiResponse;
use App\Http\Resources\UserResource;

// Response with data and a custom message/status code
return ApiResponse::success(
    new UserResource($user),
    'User created successfully.',
    201
);

// Simple success response with no data
return ApiResponse::success(null, 'Item deleted.');
```

**CRITICAL**: The `error()` method has been deliberately removed from this class. **Never use this helper to return a manual error response.** All errors must be communicated by throwing an exception.

---

## 13. Controller Rules (Very Important)

Controllers should be lean and focused on orchestrating requests, not on handling complex logic or response formatting.

### ✅ DO:

1.  **Use `ApiResponse::success()` for non-paginated and `ApiResponse::paginated()` for paginated successful outcomes.**
2.  **Throw a specific, custom exception for any anticipated error.**
3.  Rely on Form Requests for input validation.

### ❌ DO NOT:

1.  **Never return a manual JSON response.** (`return response()->json([...])`)
2.  **Avoid `try/catch` blocks.** The global handler is designed to catch exceptions. Only use `try/catch` if you need to perform a specific action upon catching an error before re-throwing it.
3.  **Never throw a generic `\Exception` or `\Throwable`.** Always use a specific exception that extends `BaseApiException`.
4.  **Manually build pagination responses.** Always use the `ApiResponse::paginated()` helper.

---

## 14. Best Practices

-   **Be Specific**: Create and use exceptions that are meaningful and describe the exact error (e.g., `PaymentFailedException` is better than a generic `OrderException`).
-   **Keep Controllers Clean**: Delegate business logic to Services. Controllers should validate input, call a service method, and return a success response. The service should throw exceptions on failure.
-   **Secure by Default**: Never expose internal error details or stack traces in a production environment. The `ExceptionRegistrar` handles this automatically.

---

## 15. Logging Strategy

The system provides a baseline logging strategy, but manual logging is sometimes required.

-   **System Exceptions are Logged Automatically**: Any unhandled exception caught by the `ExceptionRegistrar` (e.g., database connection errors, code bugs) is automatically logged as a critical error.
-   **Domain Exceptions are NOT Logged by Default**: Predictable business logic errors (e.g., `InvalidCredentialsException`) are generally not logged, as they are expected behavior and not system failures.
-   **Manual Logging for Critical Domain Events**: For certain critical domain failures, manual logging is appropriate. This is especially true for payment-related issues where a permanent record of the failure is necessary for auditing or debugging.

#### ✅ Correct Manual Logging Example:
```php
try {
    $this->stripeService->createCharge($amount, $token);
} catch (Stripe\Exception\CardException $e) {
    // Log the critical payment failure before throwing our own exception.
    logger()->warning('Stripe card charge failed.', [
        'customer_id' => $this->customer->id,
        'error_message' => $e->getMessage(),
    ]);

    throw new PaymentFailedException();
}
```

---

## 16. When `try/catch` IS Allowed

While `try/catch` blocks should be avoided in controllers, they are necessary in the service layer for one primary reason: **transforming third-party exceptions into application-specific domain exceptions.**

This practice decouples our application from the libraries it uses. If we later switch from Stripe to another payment provider, we only need to update the service—the rest of the application still understands `PaymentFailedException`.

#### ✅ Correct `try/catch` for Exception Transformation:
```php
// In a service class, NOT a controller.
use App\Exceptions\Payment\StripeServiceException;
use Exception; // Catching a generic Exception from an external library.

class StripeService
{
    public function charge()
    {
        try {
            // Call to an external SDK that might throw its own exceptions.
            $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
            $stripe->charges->create([...]);
        } catch (Exception $e) {
            // Log the original error for debugging.
            logger()->error('Stripe API call failed.', ['exception' => $e]);

            // Throw our own domain-specific exception.
            throw new StripeServiceException();
        }
    }
}
```

---

## 17. AI Agent Rules

For AI agents and automated tooling, the following rules are non-negotiable:

1.  **ALWAYS Throw Exceptions for Errors**: For any non-successful outcome, throw a specific exception that extends `BaseApiException`.
2.  **NEVER Return JSON Manually**: Do not use `response()->json()`. The `ExceptionRegistrar` is the single source of truth for error responses.
3.  **ALWAYS Use `ApiResponse::success()` or `ApiResponse::paginated()`**: For all successful outcomes, use the appropriate `ApiResponse` helper. For paginated data, `ApiResponse::paginated()` is mandatory.
4.  **ALWAYS Use Existing `ErrorCode` Enum**: Do not invent new error code strings. Add new codes to the `ErrorCode` enum if a suitable one does not exist.
5.  **FOLLOW Existing Domain Folders**: Place new exceptions in the correct domain folder (`Auth`, `Order`, `Payment`).
6.  **NEVER Manually Construct Pagination**: Do not build pagination responses by hand or rely on resource collections for pagination metadata. The `paginator` instance must be passed to the `ApiResponse::paginated()` method.

---

## 18. Anti-Patterns (Critical Section)

The following practices violate the principles of this system and MUST be avoided.

-   **Returning JSON from a Controller**:
    ```php
    // ❌ WRONG
    if (!$user) {
        return response()->json(['message' => 'Not Found'], 404);
    }
    ```
    ```php
    // ✅ RIGHT
    if (!$user) {
        throw new NotFoundException('User not found.');
    }
    ```

-   **Catching and Swallowing Exceptions**:
    ```php
    // ❌ WRONG
    try {
        $this->paymentService->process($order);
    } catch (PaymentFailedException $e) {
        // The error is ignored, and the client receives a false success response.
    }
    return ApiResponse::success(null, 'Order processed.');
    ```

-   **Using `ValidationException` for Business Logic**: `ValidationException` is reserved for input validation only. Do not throw it manually for domain errors.

---

## 19. Exceptions to the JSON API Rule

In some specific scenarios, the application intentionally deviates from the standard JSON API response format. This is typically when integrating with third-party services that rely on HTTP redirects, such as OAuth providers or payment gateways.

**Key Scenarios:**
-   **OAuth Redirects**: When using packages like Laravel Socialite for Google or GitHub authentication, the application must redirect the user to the provider's authorization URL.
-   **Payment Gateway Redirects**: Services like Stripe Checkout require redirecting the user to a secure, hosted payment page.

**In these cases, the following rules apply:**

-   **Redirect Responses are Allowed**: It is correct to return a `RedirectResponse` from the controller.
-   **`try/catch` is Permitted**: `try/catch` blocks are necessary to handle exceptions from the external service's SDK (e.g., connection errors, invalid API keys).
-   **Exceptions are NOT Thrown Globally**: Application-specific exceptions (extending `BaseApiException`) should not be thrown, as the global handler would incorrectly attempt to format a JSON error response.
-   **Errors are Passed via Redirect**: If an error occurs, it should be communicated back to the frontend by passing an error code or message as a query parameter in the redirect URL.

#### ✅ Correct Redirect with Error Example:
```php
// In a controller handling a Google OAuth callback
try {
    $socialiteUser = Socialite::driver('google')->user();
    // ... process user
} catch (Exception $e) {
    // The user is redirected back to the frontend with an error parameter.
    // The frontend is responsible for displaying the error message.
    return redirect(config('app.frontend_url') . '/auth/callback?error=google_auth_failed');
}
```

---

## 20. Example Flow: User Login

This example demonstrates the complete request lifecycle.

1.  **Request Received**: A `POST` request is sent to `/api/login` with an email and password.
2.  **Validation**: The `LoginUserRequest` Form Request automatically validates the input.
    -   **Failure**: If validation fails, a `ValidationException` is thrown and the `ExceptionRegistrar` returns a `422` response with error details. The controller method is never executed.
3.  **Controller Action**: The `login` method in `AuthController` is called.
4.  **Business Logic**:
    -   The controller attempts to find the user by email.
    -   It checks if the user exists and if the password is correct.
    -   **Failure**: If credentials do not match, it throws `new InvalidCredentialsException()`. The `ExceptionRegistrar` catches this and returns a `401` response with the `AUTH_001` error code.
5.  **Success**:
    -   If credentials are valid, a Sanctum token is created.
    -   The controller returns `ApiResponse::success([...])` with the user data and token. The client receives a `200` success response.