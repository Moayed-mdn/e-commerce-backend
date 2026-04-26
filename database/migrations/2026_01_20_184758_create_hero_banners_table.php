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
        Schema::create('hero_banners', function (Blueprint $table) {
            $table->id();
            $table->string('cat_url');
            $table->unsignedTinyInteger('position')->default(1);
            $table->enum('visual_type', ['image', 'gradient']);
            $table->string('image_path')->nullable();
            $table->string('gradient_from')->nullable();
            $table->string('gradient_to')->nullable();
            
            // Link fields
            $table->string('link_url')->nullable();
            $table->string('link_text')->nullable();
            $table->enum('link_target', ['_self', '_blank'])->default('_self');
            
            // Visibility 
            $table->boolean('is_active')->default(true);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hero_banners');
    }
};
