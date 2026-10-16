<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BrailleDictionary extends Model
{
    use HasFactory;

    protected $fillable = [
        'character',
        'category',
        'dots',
        'unicode',
        'name',
        'pronounce',
        'description',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'dots' => 'array',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];
}
