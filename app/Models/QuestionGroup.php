<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionGroup extends Model
{
    public function section()
    {
        return $this->belongsTo(
            QuestionSection::class,
            'question_section_id'
        );
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }



    protected $fillable = [
        'question_section_id',
        'title',
        'content',
        'position',
    ];
}
