<?php

namespace Database\Seeders;

use App\Models\Device;
use Illuminate\Database\Seeder;

class DeviceSeeder extends Seeder
{
    public function run(): void
    {
        Device::updateOrCreate(
            ['device_code' => 'TACTIPAD-01'],
            [
                'name' => 'TACTIPAD 5-Cell Braille Display',
                'connection_type' => 'bluetooth',
                'battery_level' => 100,
                'firmware_version' => 'v2.1.0-BLE',
                'status' => 'standby',
                'last_connected_at' => now(),
            ]
        );
    }
}
