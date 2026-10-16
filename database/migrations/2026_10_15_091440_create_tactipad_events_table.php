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
        if (!Schema::hasTable('tactipad_events')) {
            Schema::create('tactipad_events', function (Blueprint $table) {
                $table->id();
                $table->string('device_id')->default('TACTIPAD-01')->index();
                $table->string('action'); // dot_pressed, dot_released, reset, letter_formed, sync
                $table->unsignedTinyInteger('dot_index')->nullable(); // 1 - 6
                $table->json('active_dots')->nullable(); // [1, 2, 4]
                $table->string('recognized_char')->nullable();
                $table->string('source')->default('simulator'); // hardware, web_serial, simulator, api
                $table->json('raw_payload')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tactipad_events');
    }
};
