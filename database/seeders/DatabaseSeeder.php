<?php
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            BrandSeeder::class,       // ← NEW (must be before products)
            CategorySeeder::class,
            ProductSeeder::class,
            FakeSalesSeeder::class,
            ReviewSeeder::class,      // ← NEW (must be after products & users)
            HeroBannerSeeder::class,
        ]);
    }
}