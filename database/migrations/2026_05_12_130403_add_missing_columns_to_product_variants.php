<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            // Add missing columns that are validated in CreateProductRequest
            // but not yet in the table schema
            
            if (!Schema::hasColumn('product_variants', 'barcode')) {
                $table->string('barcode', 100)->nullable()->after('sku');
            }
            
            // compare_at_price and cost_price already exist per original migration ✓
            
            // low_stock_threshold and track_inventory already exist per original migration ✓
            
            if (!Schema::hasColumn('product_variants', 'weight')) {
                $table->decimal('weight', 10, 3)->nullable()->after('track_inventory');
            }
            
            if (!Schema::hasColumn('product_variants', 'weight_unit')) {
                $table->string('weight_unit', 10)->nullable()->after('weight'); // g, kg, lb
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn(['barcode', 'weight', 'weight_unit']);
        });
    }
};
