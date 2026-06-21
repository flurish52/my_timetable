<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionSection extends Model
{
    public function pastQuestion()
    {
        return $this->belongsTo(PastQuestion::class);
    }

    public function groups()
    {
        return $this->hasMany(QuestionGroup::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }


    protected $fillable = [
        'past_question_id',
        'title',
        'instructions',
        'position',
    ];
}
