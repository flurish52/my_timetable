<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionMedia extends Model
{
    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    protected $fillable = [
        'question_id',
        'type',
        'file_path',
        'caption',
        'position',
    ];
}
