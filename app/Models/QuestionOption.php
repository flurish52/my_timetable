<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionOption extends Model
{
    public function question()
    {
        return $this->belongsTo(Question::class);
    }
    public function attemptAnswers()
    {
        return $this->hasMany(QuestionAttemptAnswer::class);
    }

    protected $fillable = [
        'question_id',
        'option_text',
        'is_correct',
    ];
}
