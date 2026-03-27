<?php
namespace App\Providers;

use App\Models\Address;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Policies\AddressPolicy;
use App\Policies\OrderPolicy;
use App\Policies\PaymentMethodPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Address::class => AddressPolicy::class,
        Order::class => OrderPolicy::class,
        PaymentMethod::class => PaymentMethodPolicy::class,
    ];

    public function boot()
    {
        $this->registerPolicies();
    }
}