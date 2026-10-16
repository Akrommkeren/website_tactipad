<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TactipadEvent extends Model
{
    protected $fillable = [
        'device_id',
        'action',
        'dot_index',
        'active_dots',
        'recognized_char',
        'source',
        'raw_payload',
    ];

    protected $casts = [
        'active_dots' => 'array',
        'raw_payload' => 'array',
        'dot_index' => 'integer',
    ];
}
