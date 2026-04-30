<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StoreSeeder extends Seeder
{
    public function run(): void
    {
        // Create the test store
        $store = Store::firstOrCreate(
            ['slug' => 'test-store'],
            [
                'name' => 'Test Store',
                'is_active' => true,
            ]
        );

        // Create users (one per role)
        $superAdminUser = User::firstOrCreate(
            ['email' => 'super@test.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
            ]
        );

        $storeAdminUser = User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Store Admin',
                'password' => Hash::make('password'),
            ]
        );

        $staffUser = User::firstOrCreate(
            ['email' => 'staff@test.com'],
            [
                'name' => 'Staff User',
                'password' => Hash::make('password'),
            ]
        );

        $customerUser = User::firstOrCreate(
            ['email' => 'customer@test.com'],
            [
                'name' => 'Customer User',
                'password' => Hash::make('password'),
            ]
        );

        // Assign Spatie roles
        // super_admin -> global role (no team scope)
        $superAdminUser->assignRole(RoleEnum::SUPER_ADMIN);

        // store_admin -> store-scoped role (with team scope)
        $storeAdminUser->assignRole(RoleEnum::STORE_ADMIN, $store->id);

        // staff -> store-scoped role (with team scope)
        $staffUser->assignRole(RoleEnum::STAFF, $store->id);

        // customer -> global role (no team scope)
        $customerUser->assignRole(RoleEnum::CUSTOMER);

        // Attach users to store via pivot table (except customer)
        // super_admin user -> attach with pivot role store_admin
        if (!$store->users()->where('user_id', $superAdminUser->id)->exists()) {
            $store->users()->attach($superAdminUser->id, ['role' => 'store_admin']);
        }

        // store_admin user -> attach with pivot role store_admin
        if (!$store->users()->where('user_id', $storeAdminUser->id)->exists()) {
            $store->users()->attach($storeAdminUser->id, ['role' => 'store_admin']);
        }

        // staff user -> attach with pivot role staff
        if (!$store->users()->where('user_id', $staffUser->id)->exists()) {
            $store->users()->attach($staffUser->id, ['role' => 'staff']);
        }

        // customer user -> NOT attached to store (customers are not store members)
    }
}
