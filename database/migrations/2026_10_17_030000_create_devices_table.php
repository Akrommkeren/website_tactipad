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
        if (!Schema::hasTable('devices')) {
            Schema::create('devices', function (Blueprint $table) {
                $table->id();
                $table->string('device_code')->unique()->default('TACTIPAD-01');
                $table->string('name')->default('TACTIPAD Braille Board');
                $table->enum('connection_type', ['bluetooth', 'usb_serial', 'wifi', 'simulator'])->default('bluetooth');
                $table->string('bluetooth_mac')->nullable();
                $table->string('ip_address')->nullable();
                $table->unsignedTinyInteger('battery_level')->nullable()->default(100);
                $table->string('firmware_version')->default('v2.1.0-BLE');
                $table->enum('status', ['connected', 'disconnected', 'standby'])->default('disconnected');
                $table->timestamp('last_connected_at')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
