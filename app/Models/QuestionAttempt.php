<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionAttempt extends Model
{
    protected $fillable = [
        'user_id',
        'past_question_id',
        'score',
        'total_questions',
        'correct_answers',
        'started_at',
        'submitted_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pastQuestion()
    {
        return $this->belongsTo(PastQuestion::class);
    }

    public function answers()
    {
        return $this->hasMany(QuestionAttemptAnswer::class);
    }
}
