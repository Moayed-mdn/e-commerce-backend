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
        Schema::create('variant_attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variant_id')->constrained('product_variants')->cascadeOnDelete();
            $table->foreignId('attribute_id')->constrained('attributes')->cascadeOnDelete();
            $table->foreignId('attribute_value_id')->constrained('attribute_values')->cascadeOnDelete();

            // The Problem:
            //$table->unique(['variant_id', 'attribute_value_id'], 'variant_value_unique'); abd WITHOUT  $table->foreignId('attribute_id')->constrained('attributes')->cascadeOnDelete();
            // This doesn't actually stop a variant from having two colors. It only stops a variant from having the exact same color twice.
            // It would allow: Variant #1 -> Black AND Variant #1 -> Blue.
            // This is bad because a single phone variant can't be two colors at once.   
            
            
            // Ensure a variant can't have two colors or two storages
            $table->unique(['variant_id', 'attribute_id'], 'variant_value_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variant_attribute_values');
    }
};
