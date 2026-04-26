<?php

declare(strict_types=1);

/**
 * Domain-Driven Architecture Refactoring Script
 * 
 * This script moves files into their correct domain subdirectories,
 * updates namespaces, and fixes all use statements throughout the codebase.
 */

// Domain mappings for Actions
$actionDomains = [
    // Auth domain
    'LoginUserAction.php' => 'Auth',
    'LogoutUserAction.php' => 'Auth',
    'RegisterUserAction.php' => 'Auth',
    'VerifyEmailAction.php' => 'Auth',
    'ResendVerificationEmailAction.php' => 'Auth',
    'SocialAuthRedirectAction.php' => 'Auth',
    'SocialAuthCallbackAction.php' => 'Auth',
    'GetMeAction.php' => 'Auth',
    'ChangePasswordAction.php' => 'Auth',
    'DeleteAccountAction.php' => 'Auth',
    'GetProfileAction.php' => 'Auth',
    'UpdateProfileAvatarAction.php' => 'Auth',
    'UpdateProfileInfoAction.php' => 'Auth',
    'UpdateProfilePasswordAction.php' => 'Auth',
    'ResetPasswordAction.php' => 'Auth',
    'SendResetLinkAction.php' => 'Auth',
    'UpdateUserAction.php' => 'Auth',
    'GetUserAction.php' => 'Auth',
    
    // Cart domain
    'AddToCartAction.php' => 'Cart',
    'ClearCartAction.php' => 'Cart',
    'GetCartAction.php' => 'Cart',
    'RemoveCartItemAction.php' => 'Cart',
    'UpdateCartItemAction.php' => 'Cart',
    
    // Order domain
    'CreateOrderAction.php' => 'Order',
    'CancelOrderAction.php' => 'Order',
    'GetOrderAction.php' => 'Order',
    'ListOrdersAction.php' => 'Order',
    
    // Product domain
    'GetProductDetailAction.php' => 'Product',
    'GetRelatedProductsAction.php' => 'Product',
    'ListProductsAction.php' => 'Product',
    'GetBestSellersAction.php' => 'Product',
    'FilterProductsAction.php' => 'Product',
    'FilterProductsByCategoryAction.php' => 'Product',
    
    // Category domain
    'GetCategoriesAction.php' => 'Category',
    'GetCategoryAction.php' => 'Category',
    'GetCategoryBreadcrumbAction.php' => 'Category',
    'GetProductsByCategoryAction.php' => 'Category',
    
    // Payment domain
    'CreateCheckoutSessionAction.php' => 'Payment',
    'HandleStripeWebhookAction.php' => 'Payment',
    'GetCheckoutStatusAction.php' => 'Payment',
    
    // Address domain
    'StoreAddressAction.php' => 'Address',
    'UpdateAddressAction.php' => 'Address',
    'DeleteAddressAction.php' => 'Address',
    'ListAddressesAction.php' => 'Address',
    'SetDefaultAddressAction.php' => 'Address',
    
    // PaymentMethod domain
    'StorePaymentMethodAction.php' => 'PaymentMethod',
    'UpdatePaymentMethodAction.php' => 'PaymentMethod',
    'DeletePaymentMethodAction.php' => 'PaymentMethod',
    'ListPaymentMethodsAction.php' => 'PaymentMethod',
    'SetDefaultPaymentMethodAction.php' => 'PaymentMethod',
    
    // Homepage/Banner domain
    'GetHeroBannersAction.php' => 'Homepage',
];

// Domain mappings for DTOs
$dtoDomains = [
    // Auth domain
    'LoginUserDTO.php' => 'Auth',
    'LogoutDTO.php' => 'Auth',
    'RegisterUserDTO.php' => 'Auth',
    'VerifyEmailDTO.php' => 'Auth',
    'ResendVerificationEmailDTO.php' => 'Auth',
    'SocialAuthRedirectDTO.php' => 'Auth',
    'SocialAuthCallbackDTO.php' => 'Auth',
    'GetMeDTO.php' => 'Auth',
    'ChangePasswordDTO.php' => 'Auth',
    'DeleteAccountDTO.php' => 'Auth',
    'GetProfileDTO.php' => 'Auth',
    'UpdateProfileAvatarDTO.php' => 'Auth',
    'UpdateProfileInfoDTO.php' => 'Auth',
    'UpdateProfilePasswordDTO.php' => 'Auth',
    'ResetPasswordDTO.php' => 'Auth',
    'SendResetLinkDTO.php' => 'Auth',
    'UpdateUserDTO.php' => 'Auth',
    'GetUserDTO.php' => 'Auth',
    
    // Cart domain
    'AddToCartDTO.php' => 'Cart',
    'ClearCartDTO.php' => 'Cart',
    'GetCartDTO.php' => 'Cart',
    'RemoveCartItemDTO.php' => 'Cart',
    'UpdateCartItemDTO.php' => 'Cart',
    
    // Order domain
    'CreateOrderDTO.php' => 'Order',
    'CancelOrderDTO.php' => 'Order',
    'GetOrderDTO.php' => 'Order',
    'ListOrdersDTO.php' => 'Order',
    
    // Product domain
    'GetProductDetailDTO.php' => 'Product',
    'GetRelatedProductsDTO.php' => 'Product',
    'ListProductsDTO.php' => 'Product',
    'GetBestSellersDTO.php' => 'Product',
    'BestSellerDTO.php' => 'Product',
    'ProductCardDTO.php' => 'Product',
    
    // Category domain
    'GetCategoriesDTO.php' => 'Category',
    'GetCategoryDTO.php' => 'Category',
    'GetProductsByCategoryDTO.php' => 'Category',
    
    // Payment domain
    'CreateCheckoutDTO.php' => 'Payment',
    'GetCheckoutStatusDTO.php' => 'Payment',
    'StripeWebhookDTO.php' => 'Payment',
    
    // Address domain
    'StoreAddressDTO.php' => 'Address',
    'UpdateAddressDTO.php' => 'Address',
    'DeleteAddressDTO.php' => 'Address',
    'ListAddressesDTO.php' => 'Address',
    'SetDefaultAddressDTO.php' => 'Address',
    
    // PaymentMethod domain
    'StorePaymentMethodDTO.php' => 'PaymentMethod',
    'UpdatePaymentMethodDTO.php' => 'PaymentMethod',
    'DeletePaymentMethodDTO.php' => 'PaymentMethod',
    'ListPaymentMethodsDTO.php' => 'PaymentMethod',
    'SetDefaultPaymentMethodDTO.php' => 'PaymentMethod',
    
    // Homepage/Banner domain
    'GetHeroBannersDTO.php' => 'Homepage',
    'HeroBannerDTO.php' => 'Homepage',
    
    // Search domain
    'SearchDTO.php' => 'Search',
];

// Domain mappings for Controllers
$controllerDomains = [
    'AuthController.php' => 'Auth',
    'SocialAuthController.php' => 'Auth',
    'CartController.php' => 'Cart',
    'OrderController.php' => 'Order',
    'ProductController.php' => 'Product',
    'CategoryController.php' => 'Category',
    'CheckoutController.php' => 'Payment',
    'StripeWebhookController.php' => 'Payment',
    'PasswordResetController.php' => 'Auth',
    'ProfileController.php' => 'Auth',
    'UserController.php' => 'Auth',
    'AddressController.php' => 'Address',
    'PaymentMethodController.php' => 'PaymentMethod',
    'HomePageController.php' => 'Homepage',
    'SearchController.php' => 'Search',
];

echo "Starting Domain-Driven Architecture Refactoring...\n\n";

$baseDir = __DIR__;
$changesCount = 0;

// ============================================
// STEP 1: Refactor Actions
// ============================================
echo "=== Refactoring Actions ===\n";

$actionsDir = $baseDir . '/app/Actions';
foreach ($actionDomains as $fileName => $domain) {
    $oldPath = $actionsDir . '/' . $fileName;
    if (!file_exists($oldPath)) {
        echo "[SKIP] Action not found: $fileName\n";
        continue;
    }
    
    $domainDir = $actionsDir . '/' . $domain;
    if (!is_dir($domainDir)) {
        mkdir($domainDir, 0755, true);
        echo "[CREATE] Directory: $domain\n";
    }
    
    $newPath = $domainDir . '/' . $fileName;
    
    // Read file content
    $content = file_get_contents($oldPath);
    
    // Update namespace
    $oldNamespace = 'namespace App\\Actions;';
    $newNamespace = 'namespace App\\Actions\\' . $domain . ';';
    $content = str_replace($oldNamespace, $newNamespace, $content);
    
    // Write to new location
    file_put_contents($newPath, $content);
    
    // Remove old file
    unlink($oldPath);
    
    echo "[MOVE] $fileName -> $domain/$fileName\n";
    $changesCount++;
}

// ============================================
// STEP 2: Refactor DTOs
// ============================================
echo "\n=== Refactoring DTOs ===\n";

$dtosDir = $baseDir . '/app/DTOs';
foreach ($dtoDomains as $fileName => $domain) {
    $oldPath = $dtosDir . '/' . $fileName;
    if (!file_exists($oldPath)) {
        echo "[SKIP] DTO not found: $fileName\n";
        continue;
    }
    
    $domainDir = $dtosDir . '/' . $domain;
    if (!is_dir($domainDir)) {
        mkdir($domainDir, 0755, true);
        echo "[CREATE] Directory: $domain\n";
    }
    
    $newPath = $domainDir . '/' . $fileName;
    
    // Read file content
    $content = file_get_contents($oldPath);
    
    // Update namespace
    $oldNamespace = 'namespace App\\DTOs;';
    $newNamespace = 'namespace App\\DTOs\\' . $domain . ';';
    $content = str_replace($oldNamespace, $newNamespace, $content);
    
    // Write to new location
    file_put_contents($newPath, $content);
    
    // Remove old file
    unlink($oldPath);
    
    echo "[MOVE] $fileName -> $domain/$fileName\n";
    $changesCount++;
}

// ============================================
// STEP 3: Refactor Controllers
// ============================================
echo "\n=== Refactoring Controllers ===\n";

$controllersDir = $baseDir . '/app/Http/Controllers/Api';
foreach ($controllerDomains as $fileName => $domain) {
    $oldPath = $controllersDir . '/' . $fileName;
    if (!file_exists($oldPath)) {
        echo "[SKIP] Controller not found: $fileName\n";
        continue;
    }
    
    $domainDir = $controllersDir . '/' . $domain;
    if (!is_dir($domainDir)) {
        mkdir($domainDir, 0755, true);
        echo "[CREATE] Directory: $domain\n";
    }
    
    $newPath = $domainDir . '/' . $fileName;
    
    // Read file content
    $content = file_get_contents($oldPath);
    
    // Update namespace
    $oldNamespace = 'namespace App\\Http\\Controllers\\Api;';
    $newNamespace = 'namespace App\\Http\\Controllers\\Api\\' . $domain . ';';
    $content = str_replace($oldNamespace, $newNamespace, $content);
    
    // Write to new location
    file_put_contents($newPath, $content);
    
    // Remove old file
    unlink($oldPath);
    
    echo "[MOVE] $fileName -> $domain/$fileName\n";
    $changesCount++;
}

// ============================================
// STEP 4: Refactor Repositories (move flat ones into domains)
// ============================================
echo "\n=== Refactoring Repositories ===\n";

$reposDir = $baseDir . '/app/Repositories';

// Move flat repositories to their domain folders
$repoMappings = [
    'AddressRepository.php' => 'Address',
    'CartItemRepository.php' => 'Cart',
    'CartRepository.php' => 'Cart',
    'PaymentMethodRepository.php' => 'PaymentMethod',
    'ProductVariantRepository.php' => 'Product',
];

foreach ($repoMappings as $fileName => $domain) {
    $oldPath = $reposDir . '/' . $fileName;
    if (!file_exists($oldPath)) {
        echo "[SKIP] Repository not found: $fileName\n";
        continue;
    }
    
    $domainDir = $reposDir . '/' . $domain;
    if (!is_dir($domainDir)) {
        mkdir($domainDir, 0755, true);
        echo "[CREATE] Directory: $domain\n";
    }
    
    $newPath = $domainDir . '/' . $fileName;
    
    // Read file content
    $content = file_get_contents($oldPath);
    
    // Update namespace
    $oldNamespace = 'namespace App\\Repositories;';
    $newNamespace = 'namespace App\\Repositories\\' . $domain . ';';
    $content = str_replace($oldNamespace, $newNamespace, $content);
    
    // Write to new location
    file_put_contents($newPath, $content);
    
    // Remove old file
    unlink($oldPath);
    
    echo "[MOVE] $fileName -> $domain/$fileName\n";
    $changesCount++;
}

echo "\n=== Phase 1 Complete: Files Moved ===\n";
echo "Total files moved: $changesCount\n";
echo "\nPhase 2 will update all use statements across the codebase.\n";
