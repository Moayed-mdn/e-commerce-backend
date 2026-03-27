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
        Schema::create('hero_banner_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hero_banner_id')->constrained('hero_banners')->cascadeOnDelete();
            $table->string('locale',5);
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->string('cta_text'); // cal to action
            $table->timestamps();
            $table->unique(['hero_banner_id','locale']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hero_banner_translations');
    }
};
