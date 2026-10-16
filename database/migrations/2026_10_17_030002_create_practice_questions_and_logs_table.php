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
        if (!Schema::hasTable('practice_questions')) {
            Schema::create('practice_questions', function (Blueprint $table) {
                $table->id();
                $table->string('category', 50)->index(); // huruf, kata, angka, angka_5_digit
                $table->text('prompt_text');             // e.g. Tuliskan huruf "B"
                $table->string('target_text', 50);       // e.g. B, KACA, 12345
                $table->json('expected_cells');          // {"1": [1, 2]} or {"1": [1, 3], "2": [1], ...}
                $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('easy');
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('practice_logs')) {
            Schema::create('practice_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('question_id')->nullable()->constrained('practice_questions')->nullOnDelete();
                $table->string('device_code')->default('TACTIPAD-01')->index();
                $table->string('user_answer', 100)->nullable();
                $table->boolean('is_correct')->default(false)->index();
                $table->unsignedInteger('duration_seconds')->default(0);
                $table->json('cell_states')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('practice_logs');
        Schema::dropIfExists('practice_questions');
    }
};
