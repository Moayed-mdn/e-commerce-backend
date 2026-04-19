<?php

namespace App\Enums;

enum ErrorCode: string
{
    // --- Authentication (AUTH) ---
    case AUTH_001 = 'AUTH_001'; // Invalid credentials
    case AUTH_002 = 'AUTH_002'; // Unauthorized access
    case AUTH_003 = 'AUTH_003'; // Email not verified
    case AUTH_004 = 'AUTH_004'; // CSRF token mismatch
    case AUTH_005 = 'AUTH_005'; // Password reset failed
    case AUTH_006 = 'AUTH_006'; // Social authentication failed
    case AUTH_007 = 'AUTH_007'; // Email verification failed
    case AUTH_008 = 'AUTH_008'; // Too many requests

    // --- Order (ORD) ---
    case ORD_001 = 'ORD_001'; // Order not found
    case ORD_002 = 'ORD_002'; // Order cancellation failed
    case ORD_003 = 'ORD_003'; // Reorder failed

    // --- Payment (PMT) ---
    case PMT_001 = 'PMT_001'; // Payment failed
    case PMT_002 = 'PMT_002'; // Out of stock during payment
    case PMT_003 = 'PMT_003'; // Stripe webhook error
    case PMT_004 = 'PMT_004'; // Stripe service error

    // --- System (SYS) ---
    case SYS_001 = 'SYS_001'; // Generic server error
    case SYS_002 = 'SYS_002'; // Not Found

    // --- Validation (VAL) ---
    case VAL_001 = 'VAL_001'; // Validation failed

    // --- Product (PRD) ---
    case PRD_001 = 'PRD_001'; // Product not found
}