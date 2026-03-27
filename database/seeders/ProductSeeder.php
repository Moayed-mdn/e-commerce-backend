<?php
// database/seeders/ProductSeeder.php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Image;
use App\Models\ProductVariant;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\VariantAttributeValue;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Product name → Brand name mapping.
     */
    private array $productBrands = [
        // Phones
        'iPhone 14'              => 'Apple',
        'Samsung Galaxy S23'     => 'Samsung',
        'Xiaomi Redmi Note 12'   => 'Xiaomi',
        // Laptops
        'MacBook Pro'            => 'Apple',
        'Dell XPS 13'            => 'Dell',
        'HP Spectre x360'        => 'HP',
        'Lenovo ThinkPad X1'     => 'Lenovo',
        'ASUS ROG Zephyrus'      => 'ASUS',
        // Shoes
        'Running Sneakers'       => 'Nike',
        // Furniture
        'Modern Sofa 3-Seater'   => 'IKEA',
        'Bookshelf 5-Tier'       => 'IKEA',
    ];

    public function run()
    {
        $data = [
            'Phones' => [
                'iPhone 14' => [
                    ['Storage' => '128GB', 'Color' => 'Black'],
                    ['Storage' => '256GB', 'Color' => 'Blue'],
                ],
                'Samsung Galaxy S23' => [
                    ['Storage' => '128GB', 'Color' => 'Black'],
                    ['Storage' => '256GB', 'Color' => 'Green'],
                ],
                'Xiaomi Redmi Note 12' => [
                    ['Storage' => '64GB', 'Color' => 'Grey'],
                    ['Storage' => '128GB', 'Color' => 'Blue'],
                ],
            ],
            'Accessories' => [
                'Wireless Earbuds' => [
                    ['Model' => 'AirPods Pro', 'Color' => 'White', 'Battery' => '6h'],
                    ['Model' => 'Samsung Galaxy Buds', 'Color' => 'Black', 'Battery' => '5h'],
                    ['Model' => 'Sony WF-1000XM4', 'Color' => 'Black', 'Battery' => '8h'],
                ],
                'Smartwatches' => [
                    ['Model' => 'Apple Watch Series 8', 'Size' => '45mm', 'Color' => 'Midnight'],
                    ['Model' => 'Samsung Galaxy Watch 5', 'Size' => '44mm', 'Color' => 'Graphite'],
                    ['Model' => 'Fitbit Versa 4', 'Size' => '40mm', 'Color' => 'Black'],
                ],
                'Laptop Bags' => [
                    ['Size' => '13-14 inch', 'Color' => 'Black', 'Material' => 'Nylon'],
                    ['Size' => '15-16 inch', 'Color' => 'Gray', 'Material' => 'Leather'],
                    ['Size' => '17 inch', 'Color' => 'Blue', 'Material' => 'Polyester'],
                ],
                'External Monitors' => [
                    ['Size' => '24 inch', 'Resolution' => '1080p', 'Refresh Rate' => '75Hz'],
                    ['Size' => '27 inch', 'Resolution' => '4K', 'Refresh Rate' => '60Hz'],
                    ['Size' => '32 inch', 'Resolution' => '1440p', 'Refresh Rate' => '144Hz'],
                ],
                'Mechanical Keyboards' => [
                    ['Layout' => 'TKL', 'Switches' => 'Blue', 'Backlight' => 'RGB'],
                    ['Layout' => 'Full Size', 'Switches' => 'Red', 'Backlight' => 'White'],
                    ['Layout' => '60%', 'Switches' => 'Brown', 'Backlight' => 'RGB'],
                ],
                'Wireless Mice' => [
                    ['DPI' => '16000', 'Buttons' => '6', 'Color' => 'Black'],
                    ['DPI' => '12000', 'Buttons' => '8', 'Color' => 'White'],
                    ['DPI' => '25600', 'Buttons' => '11', 'Color' => 'Gray'],
                ],
                'USB-C Hubs' => [
                    ['Ports' => 'HDMI + USB3 x3 + Ethernet', 'Power Delivery' => '100W'],
                    ['Ports' => '4K HDMI + USB-C + SD Card', 'Power Delivery' => '85W'],
                    ['Ports' => 'VGA + USB2 x2 + Audio', 'Power Delivery' => '60W'],
                ],
                'Portable SSDs' => [
                    ['Capacity' => '500GB', 'Interface' => 'USB-C', 'Speed' => '1050MB/s'],
                    ['Capacity' => '1TB', 'Interface' => 'USB 3.2', 'Speed' => '2000MB/s'],
                    ['Capacity' => '2TB', 'Interface' => 'Thunderbolt', 'Speed' => '2800MB/s'],
                ],
            ],
            'Laptops' => [
                'MacBook Pro' => [
                    ['Storage' => '256GB', 'RAM' => '8GB', 'Color' => 'Space Gray'],
                    ['Storage' => '512GB', 'RAM' => '16GB', 'Color' => 'Silver'],
                    ['Storage' => '1TB', 'RAM' => '32GB', 'Color' => 'Space Gray'],
                ],
                'Dell XPS 13' => [
                    ['Storage' => '256GB', 'RAM' => '8GB', 'Color' => 'Platinum Silver'],
                    ['Storage' => '512GB', 'RAM' => '16GB', 'Color' => 'Frost White'],
                    ['Storage' => '1TB', 'RAM' => '32GB', 'Color' => 'Platinum Silver'],
                ],
                'HP Spectre x360' => [
                    ['Storage' => '512GB', 'RAM' => '16GB', 'Color' => 'Nightfall Black'],
                    ['Storage' => '1TB', 'RAM' => '32GB', 'Color' => 'Poseidon Blue'],
                    ['Storage' => '2TB', 'RAM' => '32GB', 'Color' => 'Natural Silver'],
                ],
                'Lenovo ThinkPad X1' => [
                    ['Storage' => '256GB', 'RAM' => '16GB', 'Color' => 'Black'],
                    ['Storage' => '512GB', 'RAM' => '32GB', 'Color' => 'Black'],
                    ['Storage' => '1TB', 'RAM' => '32GB', 'Color' => 'Black'],
                ],
                'ASUS ROG Zephyrus' => [
                    ['Storage' => '512GB', 'RAM' => '16GB', 'Color' => 'Eclipse Gray'],
                    ['Storage' => '1TB', 'RAM' => '32GB', 'Color' => 'Moonlight White'],
                    ['Storage' => '2TB', 'RAM' => '64GB', 'Color' => 'Eclipse Gray'],
                ],
            ],
            'Men Clothing' => [
                'Classic Cotton T-Shirt' => [
                    ['Size' => 'S', 'Color' => 'White'],
                    ['Size' => 'M', 'Color' => 'White'],
                    ['Size' => 'L', 'Color' => 'White'],
                    ['Size' => 'XL', 'Color' => 'Black'],
                ],
                'Slim Fit Jeans' => [
                    ['Size' => '30', 'Color' => 'Blue'],
                    ['Size' => '32', 'Color' => 'Blue'],
                    ['Size' => '34', 'Color' => 'Black'],
                ],
                'Winter Hoodie' => [
                    ['Size' => 'M', 'Color' => 'Grey'],
                    ['Size' => 'L', 'Color' => 'Grey'],
                    ['Size' => 'XL', 'Color' => 'Black'],
                ],
            ],
            'Women Clothing' => [
                'Flory Summer Dress' => [
                    ['Size' => 'S', 'Color' => 'Pink'],
                    ['Size' => 'M', 'Color' => 'Pink'],
                    ['Size' => 'L', 'Color' => 'Blue'],
                    ['Size' => 'XL', 'Color' => 'White'],
                ],
                'High-Waist Jeans' => [
                    ['Size' => '28', 'Color' => 'Light Blue'],
                    ['Size' => '30', 'Color' => 'Light Blue'],
                    ['Size' => '32', 'Color' => 'Black'],
                    ['Size' => '34', 'Color' => 'Dark Blue'],
                ],
                'Oversized Sweater' => [
                    ['Size' => 'S', 'Color' => 'Cream'],
                    ['Size' => 'M', 'Color' => 'Cream'],
                    ['Size' => 'L', 'Color' => 'Beige'],
                    ['Size' => 'XL', 'Color' => 'Grey'],
                ],
                'Classic Blazer' => [
                    ['Size' => 'S', 'Color' => 'Black'],
                    ['Size' => 'M', 'Color' => 'Black'],
                    ['Size' => 'L', 'Color' => 'Navy'],
                    ['Size' => 'XL', 'Color' => 'Camel'],
                ],
                'Yoga Pants' => [
                    ['Size' => 'S', 'Color' => 'Black'],
                    ['Size' => 'M', 'Color' => 'Black'],
                    ['Size' => 'L', 'Color' => 'Dark Grey'],
                    ['Size' => 'XL', 'Color' => 'Burgundy'],
                ],
            ],
            'Shoes' => [
                'Running Sneakers' => [
                    ['Size' => '38', 'Color' => 'White'],
                    ['Size' => '39', 'Color' => 'White'],
                    ['Size' => '40', 'Color' => 'Black'],
                    ['Size' => '41', 'Color' => 'Blue'],
                ],
                'Leather Boots' => [
                    ['Size' => '37', 'Color' => 'Brown'],
                    ['Size' => '38', 'Color' => 'Brown'],
                    ['Size' => '39', 'Color' => 'Black'],
                    ['Size' => '40', 'Color' => 'Black'],
                ],
                'High Heels' => [
                    ['Size' => '36', 'Color' => 'Nude'],
                    ['Size' => '37', 'Color' => 'Nude'],
                    ['Size' => '38', 'Color' => 'Black'],
                    ['Size' => '39', 'Color' => 'Red'],
                ],
                'Casual Loafers' => [
                    ['Size' => '38', 'Color' => 'Brown'],
                    ['Size' => '39', 'Color' => 'Brown'],
                    ['Size' => '40', 'Color' => 'Black'],
                    ['Size' => '41', 'Color' => 'Navy'],
                ],
                'Sports Sandals' => [
                    ['Size' => '37', 'Color' => 'Black'],
                    ['Size' => '38', 'Color' => 'Black'],
                    ['Size' => '39', 'Color' => 'Blue'],
                    ['Size' => '40', 'Color' => 'Grey'],
                ],
            ],
            'Appliances' => [
                'Air Fryer 4L' => [
                    ['Capacity' => '4L', 'Color' => 'Black'],
                ],
                'Electric Kettle 1.7L' => [
                    ['Capacity' => '1.7L', 'Color' => 'Silver'],
                ],
                'Blender 1200W' => [
                    ['Power' => '1200W', 'Color' => 'Black'],
                    ['Power' => '1200W', 'Color' => 'White'],
                ],
            ],
            'Furniture' => [
                'Modern Sofa 3-Seater' => [
                    ['Dimensions' => '200x90x85cm', 'Color' => 'Grey'],
                    ['Dimensions' => '200x90x85cm', 'Color' => 'Beige'],
                    ['Dimensions' => '200x90x85cm', 'Color' => 'Navy Blue'],
                ],
                'Wooden Dining Table' => [
                    ['Dimensions' => '160x90x75cm', 'Material' => 'Oak'],
                    ['Dimensions' => '180x90x75cm', 'Material' => 'Walnut'],
                    ['Dimensions' => '200x100x75cm', 'Material' => 'Teak'],
                ],
                'Office Ergonomic Chair' => [
                    ['Color' => 'Black', 'Material' => 'Mesh'],
                    ['Color' => 'Gray', 'Material' => 'Leather'],
                ],
                'Queen Size Bed Frame' => [
                    ['Dimensions' => '160x200cm', 'Material' => 'Wood'],
                    ['Dimensions' => '160x200cm', 'Material' => 'Metal'],
                ],
                'Bookshelf 5-Tier' => [
                    ['Dimensions' => '120x30x180cm', 'Color' => 'White'],
                    ['Dimensions' => '120x30x180cm', 'Color' => 'Brown'],
                ],
            ],
            'Decor' => [
                'Ceramic Table Vase' => [
                    ['Height' => '25cm', 'Color' => 'White'],
                    ['Height' => '30cm', 'Color' => 'Blue'],
                    ['Height' => '35cm', 'Color' => 'Green'],
                ],
                'Wall Clock Modern' => [
                    ['Diameter' => '40cm', 'Style' => 'Minimalist'],
                    ['Diameter' => '50cm', 'Style' => 'Vintage'],
                    ['Diameter' => '60cm', 'Style' => 'Industrial'],
                ],
                'LED Floor Lamp' => [
                    ['Height' => '160cm', 'Color Temperature' => 'Warm White'],
                    ['Height' => '180cm', 'Color Temperature' => 'Cool White'],
                ],
                'Abstract Wall Art' => [
                    ['Dimensions' => '60x80cm', 'Frame' => 'Black'],
                    ['Dimensions' => '80x120cm', 'Frame' => 'White'],
                    ['Dimensions' => '100x150cm', 'Frame' => 'Wood'],
                ],
                'Decorative Throw Pillows' => [
                    ['Size' => '45x45cm', 'Material' => 'Cotton'],
                    ['Size' => '50x50cm', 'Material' => 'Velvet'],
                ],
                'Indoor Plant Set' => [
                    ['Set' => '3 Succulents', 'Pot Material' => 'Ceramic'],
                    ['Set' => '5 Mixed Plants', 'Pot Material' => 'Terracotta'],
                ],
            ],
            'Fitness Equipment' => [
                'Adjustable Dumbbells' => [
                    ['Weight' => '20kg'],
                    ['Weight' => '30kg'],
                ],
                'Yoga Mat Pro' => [
                    ['Thickness' => '5mm', 'Color' => 'Blue'],
                    ['Thickness' => '8mm', 'Color' => 'Purple'],
                ],
                'Pull-Up Bar' => [
                    ['Type' => 'Wall-mounted'],
                    ['Type' => 'Door-mounted'],
                ],
            ],
        ];

        DB::beginTransaction();

        foreach ($data as $categoryName => $products) {
            $category = Category::whereHas('translations', function ($query) use ($categoryName) {
                $query->where('locale', 'en')->where('name', $categoryName);
            })->first();

            if (!$category) {
                $this->command->info("❌ No $categoryName category found!");
                continue;
            }

            foreach ($products as $productName => $variants) {

                // ── Resolve brand ──────────────────────────────
                $brandId = null;
                if (isset($this->productBrands[$productName])) {
                    $brand = Brand::where('slug', Str::slug($this->productBrands[$productName]))->first();
                    $brandId = $brand?->id;
                }

                // ── Create product ─────────────────────────────
                $product = Product::create([
                    'category_id' => $category->id,
                    'brand_id'    => $brandId,
                    'is_active'   => true,
                ]);

                // ── Translations ───────────────────────────────
                $product->translations()->create([
                    'locale'      => 'en',
                    'name'        => $productName,
                    'description' => "$productName — premium quality, fast shipping.",
                    'slug'        => Str::slug($productName),
                ]);

                $product->translations()->create([
                    'locale'      => 'ar',
                    'name'        => $this->getArabicProductName($productName),
                    'description' => 'وصف ' . $this->getArabicProductName($productName) . ' — جودة ممتازة، شحن سريع.',
                    'slug'        => Str::slug($this->getArabicProductName($productName)),
                ]);

                // ── Variants ───────────────────────────────────
                $firstVariant = null;

                foreach ($variants as $attributes) {
                    $variant = ProductVariant::create([
                        'product_id'       => $product->id,
                        'sku'              => strtoupper(Str::slug($productName)) . '-' . rand(1000, 9999),
                        'price'            => rand(50, 999),
                        'quantity'         => rand(5, 50),
                        'batch_number'     => 'BATCH-' . rand(10000, 99999),
                        'manufacture_date' => now()->subMonths(rand(1, 12)),
                        'expiry_date'      => now()->addMonths(rand(6, 24)),
                        'is_active'        => true,
                    ]);

                    if (!$firstVariant) {
                        $firstVariant = $variant;
                    }

                    Image::create([
                        'imageable_id'   => $variant->id,
                        'imageable_type' => ProductVariant::class,
                        'image_url'      => '/storage/variants/default.png',
                        'is_primary'     => true,
                    ]);

                    foreach ($attributes as $attributeName => $attributeValue) {
                        $attribute = Attribute::firstOrCreate(
                            ['code' => Str::slug($attributeName)]
                        );

                        if (!$attribute->translations()->where('locale', 'en')->exists()) {
                            $attribute->translations()->create([
                                'locale' => 'en',
                                'name'   => $attributeName,
                            ]);
                        }

                        $attributeValueModel = AttributeValue::firstOrCreate([
                            'attribute_id' => $attribute->id,
                            'code'         => Str::slug($attributeValue),
                        ]);

                        if (!$attributeValueModel->translations()->where('locale', 'en')->exists()) {
                            $attributeValueModel->translations()->create([
                                'locale' => 'en',
                                'label'  => $attributeValue,
                            ]);
                        }

                        VariantAttributeValue::create([
                            'variant_id'         => $variant->id,
                            'attribute_id'       => $attribute->id,
                            'attribute_value_id' => $attributeValueModel->id,
                        ]);
                    }
                }

                // ── Set default variant ────────────────────────
                if ($firstVariant) {
                    $product->update(['product_variant_id' => $firstVariant->id]);
                }
            }
        }

        DB::commit();
        $this->command->info('✅ Products seeded with brands & default variants!');
    }

    private function getArabicProductName($englishName)
    {
        $translations = [
            'iPhone 14' => 'آيفون 14',
            'Samsung Galaxy S23' => 'سامسونج جالاكسي S23',
            'Xiaomi Redmi Note 12' => 'شاومي ريدمي نوت 12',
            'Wireless Earbuds' => 'سماعات لاسلكية',
            'Smartwatches' => 'ساعات ذكية',
            'Laptop Bags' => 'حقائب لابتوب',
            'External Monitors' => 'شاشات خارجية',
            'Mechanical Keyboards' => 'لوحات مفاتيح ميكانيكية',
            'Wireless Mice' => 'فئران لاسلكية',
            'USB-C Hubs' => 'موزعات USB-C',
            'Portable SSDs' => 'أقراص SSD محمولة',
            'MacBook Pro' => 'ماك بوك برو',
            'Dell XPS 13' => 'ديل XPS 13',
            'HP Spectre x360' => 'إتش بي سبيكتر x360',
            'Lenovo ThinkPad X1' => 'لينوفو ثينك باد X1',
            'ASUS ROG Zephyrus' => 'آسوس ROG زيفيروس',
            'Classic Cotton T-Shirt' => 'تي شيرت قطني كلاسيكي',
            'Slim Fit Jeans' => 'جينز بقصة ضيقة',
            'Winter Hoodie' => 'هودي شتوي',
            'Flory Summer Dress' => 'فستان صيفي زهري',
            'High-Waist Jeans' => 'جينز عالي الخصر',
            'Oversized Sweater' => 'كنزة صوف كبيرة',
            'Classic Blazer' => 'بليزر كلاسيكي',
            'Yoga Pants' => 'بنطلون يوجا',
            'Running Sneakers' => 'حذاء جري رياضي',
            'Leather Boots' => 'جزمة جلدية',
            'High Heels' => 'كعب عالي',
            'Casual Loafers' => 'لوففرز كاجوال',
            'Sports Sandals' => 'صنادل رياضية',
            'Air Fryer 4L' => 'مقلاة هوائية 4 لتر',
            'Electric Kettle 1.7L' => 'غلاية كهربائية 1.7 لتر',
            'Blender 1200W' => 'خلاط 1200 واط',
            'Modern Sofa 3-Seater' => 'كنبة حديثة 3 مقاعد',
            'Wooden Dining Table' => 'طاولة طعام خشبية',
            'Office Ergonomic Chair' => 'كرسي مكتب مريح',
            'Queen Size Bed Frame' => 'هيكل سرير مقاس كوين',
            'Bookshelf 5-Tier' => 'رف كتب 5 طبقات',
            'Ceramic Table Vase' => 'مزهرية سيراميك للطاولة',
            'Wall Clock Modern' => 'ساعة حائط عصرية',
            'LED Floor Lamp' => 'مصباح أرضي LED',
            'Abstract Wall Art' => 'لوحة حائط تجريدية',
            'Decorative Throw Pillows' => 'وسائد زخرفية',
            'Indoor Plant Set' => 'مجموعة نباتات داخلية',
            'Adjustable Dumbbells' => 'دمبلز قابل للتعديل',
            'Yoga Mat Pro' => 'سجادة يوجا احترافية',
            'Pull-Up Bar' => 'بار عقلة',
        ];

        return $translations[$englishName] ?? $englishName;
    }
}