<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PracticeQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'category',
        'prompt_text',
        'target_text',
        'expected_cells',
        'difficulty',
        'is_active',
    ];

    protected $casts = [
        'expected_cells' => 'array',
        'is_active' => 'boolean',
    ];

    public function logs(): HasMany
    {
        return $this->hasMany(PracticeLog::class, 'question_id');
    }
}
