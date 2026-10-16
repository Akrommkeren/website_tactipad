<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PracticeLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'device_code',
        'user_answer',
        'is_correct',
        'duration_seconds',
        'cell_states',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'duration_seconds' => 'integer',
        'cell_states' => 'array',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(PracticeQuestion::class, 'question_id');
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class, 'device_code', 'device_code');
    }
}
