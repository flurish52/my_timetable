<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionAttemptAnswer extends Model
{
    protected $fillable = [
        'question_attempt_id',
        'question_id',
        'question_option_id',
        'answer_text',
        'is_correct',
        'marks_awarded',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    public function attempt()
    {
        return $this->belongsTo(
            QuestionAttempt::class,
            'question_attempt_id'
        );
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function option()
    {
        return $this->belongsTo(
            QuestionOption::class,
            'question_option_id'
        );
    }
}
