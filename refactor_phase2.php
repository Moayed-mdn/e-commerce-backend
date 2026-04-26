<?php

declare(strict_types=1);

/**
 * Phase 2: Update all use statements across the codebase
 * 
 * This script finds and replaces old namespace references with new domain-specific ones.
 */

echo "=== Phase 2: Updating Use Statements ===\n\n";

$baseDir = __DIR__;
$updatesCount = 0;

// Define all namespace mappings
$namespaceMappings = [
    // Actions
    'App\\Actions\\ChangePasswordAction' => 'App\\Actions\\Auth\\ChangePasswordAction',
    'App\\Actions\\DeleteAccountAction' => 'App\\Actions\\Auth\\DeleteAccountAction',
    'App\\Actions\\GetProfileAction' => 'App\\Actions\\Auth\\GetProfileAction',
    'App\\Actions\\UpdateProfileAvatarAction' => 'App\\Actions\\Auth\\UpdateProfileAvatarAction',
    'App\\Actions\\UpdateProfileInfoAction' => 'App\\Actions\\Auth\\UpdateProfileInfoAction',
    'App\\Actions\\UpdateProfilePasswordAction' => 'App\\Actions\\Auth\\UpdateProfilePasswordAction',
    'App\\Actions\\ResetPasswordAction' => 'App\\Actions\\Auth\\ResetPasswordAction',
    'App\\Actions\\SendResetLinkAction' => 'App\\Actions\\Auth\\SendResetLinkAction',
    'App\\Actions\\UpdateUserAction' => 'App\\Actions\\Auth\\UpdateUserAction',
    'App\\Actions\\GetUserAction' => 'App\\Actions\\Auth\\GetUserAction',
    'App\\Actions\\LoginUserAction' => 'App\\Actions\\Auth\\LoginUserAction',
    'App\\Actions\\LogoutUserAction' => 'App\\Actions\\Auth\\LogoutUserAction',
    'App\\Actions\\RegisterUserAction' => 'App\\Actions\\Auth\\RegisterUserAction',
    'App\\Actions\\VerifyEmailAction' => 'App\\Actions\\Auth\\VerifyEmailAction',
    'App\\Actions\\ResendVerificationEmailAction' => 'App\\Actions\\Auth\\ResendVerificationEmailAction',
    'App\\Actions\\SocialAuthRedirectAction' => 'App\\Actions\\Auth\\SocialAuthRedirectAction',
    'App\\Actions\\SocialAuthCallbackAction' => 'App\\Actions\\Auth\\SocialAuthCallbackAction',
    'App\\Actions\\GetMeAction' => 'App\\Actions\\Auth\\GetMeAction',
    
    'App\\Actions\\AddToCartAction' => 'App\\Actions\\Cart\\AddToCartAction',
    'App\\Actions\\ClearCartAction' => 'App\\Actions\\Cart\\ClearCartAction',
    'App\\Actions\\GetCartAction' => 'App\\Actions\\Cart\\GetCartAction',
    'App\\Actions\\RemoveCartItemAction' => 'App\\Actions\\Cart\\RemoveCartItemAction',
    'App\\Actions\\UpdateCartItemAction' => 'App\\Actions\\Cart\\UpdateCartItemAction',
    
    'App\\Actions\\CreateOrderAction' => 'App\\Actions\\Order\\CreateOrderAction',
    'App\\Actions\\CancelOrderAction' => 'App\\Actions\\Order\\CancelOrderAction',
    'App\\Actions\\GetOrderAction' => 'App\\Actions\\Order\\GetOrderAction',
    'App\\Actions\\ListOrdersAction' => 'App\\Actions\\Order\\ListOrdersAction',
    
    'App\\Actions\\GetProductDetailAction' => 'App\\Actions\\Product\\GetProductDetailAction',
    'App\\Actions\\GetRelatedProductsAction' => 'App\\Actions\\Product\\GetRelatedProductsAction',
    'App\\Actions\\ListProductsAction' => 'App\\Actions\\Product\\ListProductsAction',
    'App\\Actions\\GetBestSellersAction' => 'App\\Actions\\Product\\GetBestSellersAction',
    'App\\Actions\\FilterProductsAction' => 'App\\Actions\\Product\\FilterProductsAction',
    'App\\Actions\\FilterProductsByCategoryAction' => 'App\\Actions\\Product\\FilterProductsByCategoryAction',
    
    'App\\Actions\\GetCategoriesAction' => 'App\\Actions\\Category\\GetCategoriesAction',
    'App\\Actions\\GetCategoryAction' => 'App\\Actions\\Category\\GetCategoryAction',
    'App\\Actions\\GetCategoryBreadcrumbAction' => 'App\\Actions\\Category\\GetCategoryBreadcrumbAction',
    'App\\Actions\\GetProductsByCategoryAction' => 'App\\Actions\\Category\\GetProductsByCategoryAction',
    
    'App\\Actions\\CreateCheckoutSessionAction' => 'App\\Actions\\Payment\\CreateCheckoutSessionAction',
    'App\\Actions\\HandleStripeWebhookAction' => 'App\\Actions\\Payment\\HandleStripeWebhookAction',
    'App\\Actions\\GetCheckoutStatusAction' => 'App\\Actions\\Payment\\GetCheckoutStatusAction',
    
    'App\\Actions\\StoreAddressAction' => 'App\\Actions\\Address\\StoreAddressAction',
    'App\\Actions\\UpdateAddressAction' => 'App\\Actions\\Address\\UpdateAddressAction',
    'App\\Actions\\DeleteAddressAction' => 'App\\Actions\\Address\\DeleteAddressAction',
    'App\\Actions\\ListAddressesAction' => 'App\\Actions\\Address\\ListAddressesAction',
    'App\\Actions\\SetDefaultAddressAction' => 'App\\Actions\\Address\\SetDefaultAddressAction',
    
    'App\\Actions\\StorePaymentMethodAction' => 'App\\Actions\\PaymentMethod\\StorePaymentMethodAction',
    'App\\Actions\\UpdatePaymentMethodAction' => 'App\\Actions\\PaymentMethod\\UpdatePaymentMethodAction',
    'App\\Actions\\DeletePaymentMethodAction' => 'App\\Actions\\PaymentMethod\\DeletePaymentMethodAction',
    'App\\Actions\\ListPaymentMethodsAction' => 'App\\Actions\\PaymentMethod\\ListPaymentMethodsAction',
    'App\\Actions\\SetDefaultPaymentMethodAction' => 'App\\Actions\\PaymentMethod\\SetDefaultPaymentMethodAction',
    
    'App\\Actions\\GetHeroBannersAction' => 'App\\Actions\\Homepage\\GetHeroBannersAction',
    
    // DTOs
    'App\\DTOs\\ChangePasswordDTO' => 'App\\DTOs\\Auth\\ChangePasswordDTO',
    'App\\DTOs\\DeleteAccountDTO' => 'App\\DTOs\\Auth\\DeleteAccountDTO',
    'App\\DTOs\\GetProfileDTO' => 'App\\DTOs\\Auth\\GetProfileDTO',
    'App\\DTOs\\UpdateProfileAvatarDTO' => 'App\\DTOs\\Auth\\UpdateProfileAvatarDTO',
    'App\\DTOs\\UpdateProfileInfoDTO' => 'App\\DTOs\\Auth\\UpdateProfileInfoDTO',
    'App\\DTOs\\UpdateProfilePasswordDTO' => 'App\\DTOs\\Auth\\UpdateProfilePasswordDTO',
    'App\\DTOs\\ResetPasswordDTO' => 'App\\DTOs\\Auth\\ResetPasswordDTO',
    'App\\DTOs\\SendResetLinkDTO' => 'App\\DTOs\\Auth\\SendResetLinkDTO',
    'App\\DTOs\\UpdateUserDTO' => 'App\\DTOs\\Auth\\UpdateUserDTO',
    'App\\DTOs\\GetUserDTO' => 'App\\DTOs\\Auth\\GetUserDTO',
    'App\\DTOs\\LoginUserDTO' => 'App\\DTOs\\Auth\\LoginUserDTO',
    'App\\DTOs\\LogoutDTO' => 'App\\DTOs\\Auth\\LogoutDTO',
    'App\\DTOs\\RegisterUserDTO' => 'App\\DTOs\\Auth\\RegisterUserDTO',
    'App\\DTOs\\VerifyEmailDTO' => 'App\\DTOs\\Auth\\VerifyEmailDTO',
    'App\\DTOs\\ResendVerificationEmailDTO' => 'App\\DTOs\\Auth\\ResendVerificationEmailDTO',
    'App\\DTOs\\SocialAuthRedirectDTO' => 'App\\DTOs\\Auth\\SocialAuthRedirectDTO',
    'App\\DTOs\\SocialAuthCallbackDTO' => 'App\\DTOs\\Auth\\SocialAuthCallbackDTO',
    'App\\DTOs\\GetMeDTO' => 'App\\DTOs\\Auth\\GetMeDTO',
    
    'App\\DTOs\\AddToCartDTO' => 'App\\DTOs\\Cart\\AddToCartDTO',
    'App\\DTOs\\ClearCartDTO' => 'App\\DTOs\\Cart\\ClearCartDTO',
    'App\\DTOs\\GetCartDTO' => 'App\\DTOs\\Cart\\GetCartDTO',
    'App\\DTOs\\RemoveCartItemDTO' => 'App\\DTOs\\Cart\\RemoveCartItemDTO',
    'App\\DTOs\\UpdateCartItemDTO' => 'App\\DTOs\\Cart\\UpdateCartItemDTO',
    
    'App\\DTOs\\CreateOrderDTO' => 'App\\DTOs\\Order\\CreateOrderDTO',
    'App\\DTOs\\CancelOrderDTO' => 'App\\DTOs\\Order\\CancelOrderDTO',
    'App\\DTOs\\GetOrderDTO' => 'App\\DTOs\\Order\\GetOrderDTO',
    'App\\DTOs\\ListOrdersDTO' => 'App\\DTOs\\Order\\ListOrdersDTO',
    
    'App\\DTOs\\GetProductDetailDTO' => 'App\\DTOs\\Product\\GetProductDetailDTO',
    'App\\DTOs\\GetRelatedProductsDTO' => 'App\\DTOs\\Product\\GetRelatedProductsDTO',
    'App\\DTOs\\ListProductsDTO' => 'App\\DTOs\\Product\\ListProductsDTO',
    'App\\DTOs\\GetBestSellersDTO' => 'App\\DTOs\\Product\\GetBestSellersDTO',
    'App\\DTOs\\BestSellerDTO' => 'App\\DTOs\\Product\\BestSellerDTO',
    'App\\DTOs\\ProductCardDTO' => 'App\\DTOs\\Product\\ProductCardDTO',
    
    'App\\DTOs\\GetCategoriesDTO' => 'App\\DTOs\\Category\\GetCategoriesDTO',
    'App\\DTOs\\GetCategoryDTO' => 'App\\DTOs\\Category\\GetCategoryDTO',
    'App\\DTOs\\GetProductsByCategoryDTO' => 'App\\DTOs\\Category\\GetProductsByCategoryDTO',
    
    'App\\DTOs\\CreateCheckoutDTO' => 'App\\DTOs\\Payment\\CreateCheckoutDTO',
    'App\\DTOs\\GetCheckoutStatusDTO' => 'App\\DTOs\\Payment\\GetCheckoutStatusDTO',
    'App\\DTOs\\StripeWebhookDTO' => 'App\\DTOs\\Payment\\StripeWebhookDTO',
    
    'App\\DTOs\\StoreAddressDTO' => 'App\\DTOs\\Address\\StoreAddressDTO',
    'App\\DTOs\\UpdateAddressDTO' => 'App\\DTOs\\Address\\UpdateAddressDTO',
    'App\\DTOs\\DeleteAddressDTO' => 'App\\DTOs\\Address\\DeleteAddressDTO',
    'App\\DTOs\\ListAddressesDTO' => 'App\\DTOs\\Address\\ListAddressesDTO',
    'App\\DTOs\\SetDefaultAddressDTO' => 'App\\DTOs\\Address\\SetDefaultAddressDTO',
    
    'App\\DTOs\\StorePaymentMethodDTO' => 'App\\DTOs\\PaymentMethod\\StorePaymentMethodDTO',
    'App\\DTOs\\UpdatePaymentMethodDTO' => 'App\\DTOs\\PaymentMethod\\UpdatePaymentMethodDTO',
    'App\\DTOs\\DeletePaymentMethodDTO' => 'App\\DTOs\\PaymentMethod\\DeletePaymentMethodDTO',
    'App\\DTOs\\ListPaymentMethodsDTO' => 'App\\DTOs\\PaymentMethod\\ListPaymentMethodsDTO',
    'App\\DTOs\\SetDefaultPaymentMethodDTO' => 'App\\DTOs\\PaymentMethod\\SetDefaultPaymentMethodDTO',
    
    'App\\DTOs\\GetHeroBannersDTO' => 'App\\DTOs\\Homepage\\GetHeroBannersDTO',
    'App\\DTOs\\HeroBannerDTO' => 'App\\DTOs\\Homepage\\HeroBannerDTO',
    
    'App\\DTOs\\SearchDTO' => 'App\\DTOs\\Search\\SearchDTO',
    
    // Controllers
    'App\\Http\\Controllers\\Api\\AuthController' => 'App\\Http\\Controllers\\Api\\Auth\\AuthController',
    'App\\Http\\Controllers\\Api\\SocialAuthController' => 'App\\Http\\Controllers\\Api\\Auth\\SocialAuthController',
    'App\\Http\\Controllers\\Api\\CartController' => 'App\\Http\\Controllers\\Api\\Cart\\CartController',
    'App\\Http\\Controllers\\Api\\OrderController' => 'App\\Http\\Controllers\\Api\\Order\\OrderController',
    'App\\Http\\Controllers\\Api\\ProductController' => 'App\\Http\\Controllers\\Api\\Product\\ProductController',
    'App\\Http\\Controllers\\Api\\CategoryController' => 'App\\Http\\Controllers\\Api\\Category\\CategoryController',
    'App\\Http\\Controllers\\Api\\CheckoutController' => 'App\\Http\\Controllers\\Api\\Payment\\CheckoutController',
    'App\\Http\\Controllers\\Api\\StripeWebhookController' => 'App\\Http\\Controllers\\Api\\Payment\\StripeWebhookController',
    'App\\Http\\Controllers\\Api\\PasswordResetController' => 'App\\Http\\Controllers\\Api\\Auth\\PasswordResetController',
    'App\\Http\\Controllers\\Api\\ProfileController' => 'App\\Http\\Controllers\\Api\\Auth\\ProfileController',
    'App\\Http\\Controllers\\Api\\UserController' => 'App\\Http\\Controllers\\Api\\Auth\\UserController',
    'App\\Http\\Controllers\\Api\\AddressController' => 'App\\Http\\Controllers\\Api\\Address\\AddressController',
    'App\\Http\\Controllers\\Api\\PaymentMethodController' => 'App\\Http\\Controllers\\Api\\PaymentMethod\\PaymentMethodController',
    'App\\Http\\Controllers\\Api\\HomePageController' => 'App\\Http\\Controllers\\Api\\Homepage\\HomePageController',
    'App\\Http\\Controllers\\Api\\SearchController' => 'App\\Http\\Controllers\\Api\\Search\\SearchController',
    
    // Repositories
    'App\\Repositories\\AddressRepository' => 'App\\Repositories\\Address\\AddressRepository',
    'App\\Repositories\\CartItemRepository' => 'App\\Repositories\\Cart\\CartItemRepository',
    'App\\Repositories\\CartRepository' => 'App\\Repositories\\Cart\\CartRepository',
    'App\\Repositories\\PaymentMethodRepository' => 'App\\Repositories\\PaymentMethod\\PaymentMethodRepository',
    'App\\Repositories\\ProductVariantRepository' => 'App\\Repositories\\Product\\ProductVariantRepository',
];

// Get all PHP files
$files = [];
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($baseDir . '/app')
);
foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $files[] = $file->getPathname();
    }
}

echo "Found " . count($files) . " PHP files to process.\n\n";

// Process each file
foreach ($files as $filePath) {
    $content = file_get_contents($filePath);
    $originalContent = $content;
    $fileUpdated = false;
    
    foreach ($namespaceMappings as $oldNamespace => $newNamespace) {
        $oldUse = 'use ' . $oldNamespace . ';';
        $newUse = 'use ' . $newNamespace . ';';
        
        if (strpos($content, $oldUse) !== false) {
            $content = str_replace($oldUse, $newUse, $content);
            $fileUpdated = true;
        }
        
        // Also handle aliased imports like: use App\Actions\SomeAction as BaseAction;
        $pattern = '/use\s+' . preg_quote($oldNamespace, '/') . '\s+as\s+\w+;/';
        if (preg_match($pattern, $content)) {
            $content = preg_replace(
                $pattern,
                'use ' . $newNamespace . ' as \1;',
                $content
            );
            $fileUpdated = true;
        }
    }
    
    if ($fileUpdated) {
        file_put_contents($filePath, $content);
        $updatesCount++;
        echo "[UPDATE] " . basename($filePath) . "\n";
    }
}

echo "\n=== Phase 2 Complete ===\n";
echo "Total files updated: $updatesCount\n";
