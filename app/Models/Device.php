<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_code',
        'name',
        'connection_type',
        'bluetooth_mac',
        'ip_address',
        'battery_level',
        'firmware_version',
        'status',
        'last_connected_at',
    ];

    protected $casts = [
        'battery_level' => 'integer',
        'last_connected_at' => 'datetime',
    ];

    public function events(): HasMany
    {
        return $this->hasMany(TactipadEvent::class, 'device_id', 'device_code');
    }

    public function practiceLogs(): HasMany
    {
        return $this->hasMany(PracticeLog::class, 'device_code', 'device_code');
    }
}
