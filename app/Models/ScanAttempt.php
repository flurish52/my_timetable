<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScanAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'past_question_id',
        'status',
        'rejection_reason',
        'raw_ai_response',
        'file_paths',
    ];

    protected $casts = [
        'raw_ai_response' => 'array',
        'file_paths' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pastQuestion(): BelongsTo
    {
        return $this->belongsTo(PastQuestion::class);
    }
}
