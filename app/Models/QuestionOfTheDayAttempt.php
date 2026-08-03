<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestionOfTheDayAttempt extends Model
{
    protected $fillable = [
        'question_of_the_day_id',
        'user_id',
        'answer_text',
        'selected_option_id',
        'is_correct',
        'shared',
        'shared_with_answer',
        'attempted_at',
    ];

    protected $casts = [
        'attempted_at' => 'datetime',
        'is_correct' => 'boolean',
        'shared' => 'boolean',
        'shared_with_answer' => 'boolean',
    ];

    public function questionOfTheDay(): BelongsTo
    {
        return $this->belongsTo(QuestionOfTheDay::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
