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
        Schema::create('attribute_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attribute_id')->constrained('attributes')->cascadeOnDelete();
            $table->string('locale',5)->index();
            $table->string('name');
            $table->unique(['locale', 'attribute_id']);
            // $table->unique(['locale', 'attribute_name']); why this is not correct?
            // answering: 
//             The Scenario:
// Imagine your shop expands. You have a "Size" for Clothing (Small, Medium) and you also have a "Size" for Paper (A4, A3).
// In your database, these are two different attributes (ID 1 and ID 10) because they have different values.
// However, in English, both are named "Size".

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attribute_translations');
    }
};
