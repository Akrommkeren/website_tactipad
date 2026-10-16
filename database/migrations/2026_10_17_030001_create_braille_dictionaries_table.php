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
        if (!Schema::hasTable('braille_dictionaries')) {
            Schema::create('braille_dictionaries', function (Blueprint $table) {
                $table->id();
                $table->string('character', 10)->index(); // A, B, 1, ., ?, etc.
                $table->string('category', 30)->index();  // alphabet, number, punctuation, symbol
                $table->json('dots');                     // [1], [1, 2], [1, 4], etc.
                $table->string('unicode', 10)->nullable(); // ⠁, ⠃, etc.
                $table->string('name', 100);              // Huruf A, Angka Satu, etc.
                $table->string('pronounce', 100);         // A, Satu, Titik, etc.
                $table->text('description')->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(true)->index();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('braille_dictionaries');
    }
};
