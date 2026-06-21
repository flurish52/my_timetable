<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionAnswer extends Model
{
    public function question()
    {
        return $this->belongsTo(Question::class);
    }


    protected $fillable = [
        'question_id',
        'answer_text',
    ];
}
