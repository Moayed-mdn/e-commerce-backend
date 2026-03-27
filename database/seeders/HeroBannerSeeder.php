<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HeroBanner;
use Carbon\Carbon;

class HeroBannerSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        /*
        |--------------------------------------------------------------------------
        | Banner 1 - Image
        |--------------------------------------------------------------------------
        */
        $banner1 = HeroBanner::create([
            'cat_url'       => '/category/smartphones',
            'position'      => 1,
            'visual_type'   => 'image',
            'image_path'    => 'hero/smartphones.jpg',
            'gradient_from' => null,
            'gradient_to'   => null,
            'is_active'     => true,
            'starts_at'     => $now,
            'ends_at'       => null,
        ]);

        $banner1->translations()->createMany([
            [
                'locale'    => 'en',
                'title'     => 'Latest Smartphones',
                'subtitle'  => 'Discover the newest arrivals',
                'cta_text'  => 'Shop Now',
            ],
            [
                'locale' => 'ar',
                'title' => 'أحدث الهواتف الذكية',
                'subtitle' => 'اكتشف أحدث الموديلات',
                'cta_text' => 'تسوق الآن',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Banner 2 - Gradient
        |--------------------------------------------------------------------------
        */
        $banner2 = HeroBanner::create([
            'cat_url'       => '/category/laptops',
            'position'      => 2,
            'visual_type'   => 'gradient',
            'image_path'    => null,
            'gradient_from' => '#0F2027',
            'gradient_to'   => '#2C5364',
            'is_active'     => true,
            'starts_at'     => $now,
            'ends_at'       => $now->copy()->addMonth(),
        ]);

        $banner2->translations()->createMany([
            [
                'locale'    => 'en',
                'title'     => 'Powerful Laptops',
                'subtitle'  => 'Performance meets portability',
                'cta_text'  => 'Explore',
            ],
            [
                'locale' => 'ar',
                'title' => 'لاب توب بأداء قوي',
                'subtitle' => 'الأداء يلتقي مع سهولة التنقل',
                'cta_text' => 'اكتشف المزيد'
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Banner 3 - Image (Scheduled)
        |--------------------------------------------------------------------------
        */
        $banner3 = HeroBanner::create([
            'cat_url'       => '/category/accessories',
            'position'      => 3,
            'visual_type'   => 'image',
            'image_path'    => 'hero/accessories.jpg',
            'gradient_from' => null,
            'gradient_to'   => null,
            'is_active'     => true,
            'starts_at'     => $now->copy()->addWeek(),
            'ends_at'       => $now->copy()->addMonths(2),
        ]);

        $banner3->translations()->createMany([
            [
                'locale'    => 'en',
                'title'     => 'Must-Have Accessories',
                'subtitle'  => 'Complete your setup',
                'cta_text'  => 'View Collection',
            ],
            [
                'locale'    => 'ar',
                'title'     => 'إكسسوارات لا غنى عنها',
                'subtitle'  => 'أكمل تجهيزاتك بأفضل المنتجات',
                'cta_text'  => 'تصفح المجموعة',
            ],
        ]);
    }
}