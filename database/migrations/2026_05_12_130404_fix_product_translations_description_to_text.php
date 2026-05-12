<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Fix: product_translations.description is varchar(255), should be text
     * to support full product descriptions.
     */
    public function up(): void
    {
        Schema::table('product_translations', function (Blueprint $table) {
            $table->text('description')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_translations', function (Blueprint $table) {
            $table->string('description')->nullable()->change();
        });
    }
};
