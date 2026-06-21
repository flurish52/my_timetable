<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    public function pastQuestion()
    {
        return $this->belongsTo(PastQuestion::class);
    }

    public function section()
    {
        return $this->belongsTo(
            QuestionSection::class,
            'question_section_id'
        );
    }

    public function group()
    {
        return $this->belongsTo(
            QuestionGroup::class,
            'question_group_id'
        );
    }

    public function parent()
    {
        return $this->belongsTo(
            Question::class,
            'parent_question_id'
        );
    }

    public function children()
    {
        return $this->hasMany(
            Question::class,
            'parent_question_id'
        );
    }

    public function options()
    {
        return $this->hasMany(QuestionOption::class);
    }

    public function answers()
    {
        return $this->hasMany(QuestionAnswer::class);
    }

    public function media()
    {
        return $this->hasMany(QuestionMedia::class)
            ->orderBy('position');
    }

    public function attemptAnswers()
    {
        return $this->hasMany(QuestionAttemptAnswer::class);
    }

    protected $fillable = [
        'past_question_id',
        'question_section_id',
        'question_group_id',
        'parent_question_id',
        'question_type',
        'question_text',
        'marks',
        'position',
    ];

    protected $casts = [
        'marks' => 'integer',
    ];
}
