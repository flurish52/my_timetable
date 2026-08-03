<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuestionOfTheDay extends Model
{
    protected $fillable = ['question_id', 'date', 'scope_type', 'course_id'];

    protected $casts = ['date' => 'date'];

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(QuestionOfTheDayAttempt::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
