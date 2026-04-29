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

        // Create users with their roles
        $superAdmin = User::firstOrCreate(
            ['email' => 'super@test.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
            ]
        );
        $superAdmin->assignRole(RoleEnum::SUPER_ADMIN);

        $storeAdmin = User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Store Admin',
                'password' => Hash::make('password'),
            ]
        );
        $storeAdmin->assignRole(RoleEnum::STORE_ADMIN, $store->id);

        $staff = User::firstOrCreate(
            ['email' => 'staff@test.com'],
            [
                'name' => 'Staff User',
                'password' => Hash::make('password'),
            ]
        );
        $staff->assignRole(RoleEnum::STAFF, $store->id);

        $customer = User::firstOrCreate(
            ['email' => 'customer@test.com'],
            [
                'name' => 'Customer User',
                'password' => Hash::make('password'),
            ]
        );
        $customer->assignRole(RoleEnum::CUSTOMER);

        // Attach users to store via pivot table (except customer)
        // super_admin user → attach to store with pivot role store_admin
        $store->users()->syncWithoutDetaching([
            $superAdmin->id => ['role' => 'store_admin'],
        ]);

        // store_admin user → attach to store with pivot role store_admin
        $store->users()->syncWithoutDetaching([
            $storeAdmin->id => ['role' => 'store_admin'],
        ]);

        // staff user → attach to store with pivot role staff
        $store->users()->syncWithoutDetaching([
            $staff->id => ['role' => 'staff'],
        ]);

        // customer user → NOT attached to store (customers are not store members)
    }
}
