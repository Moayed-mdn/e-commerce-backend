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
        Schema::table('hero_banners', function (Blueprint $table) {
            $table->foreignId('store_id')->after('id')->nullable()->constrained('stores')->cascadeOnDelete();
            $table->index('store_id');
            $table->index(['store_id', 'id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hero_banners', function (Blueprint $table) {
            $table->dropIndex(['store_id', 'id']);
            $table->dropIndex(['store_id']);
            $table->dropForeign(['store_id']);
            $table->dropColumn('store_id');
        });
    }
};
