<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class StoreSeeder extends Seeder
{
    public function run(): void
    {
        // Create the test store
        $testStore = Store::firstOrCreate(
            ['slug' => 'test-store'],
            [
                'name' => 'Test Store',
                'is_active' => true,
            ]
        );

        // Create users with their roles
        $superAdminUser = User::firstOrCreate(
            ['email' => 'super@test.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password'),
            ]
        );

        $storeAdminUser = User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Store Admin',
                'password' => bcrypt('password'),
            ]
        );

        $staffUser = User::firstOrCreate(
            ['email' => 'staff@test.com'],
            [
                'name' => 'Staff User',
                'password' => bcrypt('password'),
            ]
        );

        $customerUser = User::firstOrCreate(
            ['email' => 'customer@test.com'],
            [
                'name' => 'Customer User',
                'password' => bcrypt('password'),
            ]
        );

        // Assign Spatie roles
        // super_admin - global role (no team scope)
        $superAdminUser->syncRoles([RoleEnum::SUPER_ADMIN]);

        // store_admin - store-scoped role (with team scope using store id)
        $storeAdminUser->syncRoles([$testStore->id => RoleEnum::STORE_ADMIN]);

        // staff - store-scoped role (with team scope using store id)
        $staffUser->syncRoles([$testStore->id => RoleEnum::STAFF]);

        // customer - global role (no team scope)
        $customerUser->syncRoles([RoleEnum::CUSTOMER]);

        // Attach users to store via pivot table (except customer)
        // super_admin user → attach with pivot role store_admin
        if (!$testStore->users()->where('user_id', $superAdminUser->id)->exists()) {
            $testStore->users()->attach($superAdminUser->id, ['role' => 'store_admin']);
        }

        // store_admin user → attach with pivot role store_admin
        if (!$testStore->users()->where('user_id', $storeAdminUser->id)->exists()) {
            $testStore->users()->attach($storeAdminUser->id, ['role' => 'store_admin']);
        }

        // staff user → attach with pivot role staff
        if (!$testStore->users()->where('user_id', $staffUser->id)->exists()) {
            $testStore->users()->attach($staffUser->id, ['role' => 'staff']);
        }

        // customer user → NOT attached to store (customers are not store members)
    }
}
