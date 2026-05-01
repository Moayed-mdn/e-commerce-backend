<?php

namespace App\Enums;

class PermissionEnum
{
    // User Management
    public const USER_VIEW    = 'user.view';
    public const USER_BLOCK   = 'user.block';
    public const USER_DELETE  = 'user.delete';
    public const USER_RESTORE = 'user.restore';

    // Product Management
    public const PRODUCT_VIEW    = 'product.view';
    public const PRODUCT_CREATE  = 'product.create';
    public const PRODUCT_UPDATE  = 'product.update';
    public const PRODUCT_DELETE  = 'product.delete';
    public const PRODUCT_RESTORE = 'product.restore';

    // Order Management
    public const ORDER_VIEW          = 'order.view';
    public const ORDER_UPDATE_STATUS = 'order.update_status';
    public const ORDER_CANCEL        = 'order.cancel';
    public const ORDER_REFUND        = 'order.refund';

    // Store Management
    public const STORE_UPDATE  = 'store.update';
    public const STORE_DELETE  = 'store.delete';
    public const STORE_VIEW    = 'store.view';

    // Dashboard
    public const DASHBOARD_VIEW = 'dashboard.view';
}
