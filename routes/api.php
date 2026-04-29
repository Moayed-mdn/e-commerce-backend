<?php

use Illuminate\Support\Facades\Route;

// Auth (no store context)
require 'api/v1/users/auth.php';

// Public (no store context)
require 'api/v1/users/homepage.php';
require 'api/v1/users/category.php';
require 'api/v1/users/search.php';

// Profile (no store context)
require 'api/v1/users/profile.php';

// Stripe webhook (no store context)
require 'api/v1/stripe/webhook.php';

// Store-scoped routes
require 'api/v1/stores/cart.php';
require 'api/v1/stores/orders.php';
require 'api/v1/stores/products.php';
require 'api/v1/stores/addresses.php';
require 'api/v1/stores/checkout.php';