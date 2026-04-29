<?php

namespace App\Http\Middleware;

use App\Exceptions\Store\StoreNotFoundException;
use App\Models\Store;
use Closure;
use Illuminate\Http\Request;

class StoreContext
{
    public function handle(Request $request, Closure $next): mixed
    {
        $storeId = $request->route('store');

        $store = Store::where('id', $storeId)
            ->where('is_active', true)
            ->first();

        if (!$store) {
            throw new StoreNotFoundException();
        }

        app()->instance('storeId', $store->id);
        app()->instance('currentStore', $store);

        return $next($request);
    }
}
