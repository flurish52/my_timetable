<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PastQuestion extends Model
{
    /** @use HasFactory<\Database\Factories\PastQuestionFactory> */
    use HasFactory;


    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function sections()
    {
        return $this->hasMany(QuestionSection::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }


    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function attempts()
    {
        return $this->hasMany(QuestionAttempt::class);
    }


    protected $fillable = [
        'school_id',
        'course_id',
        'semester_id',
        'session',
        'title',
        'instructions',
        'description',
        'duration_minutes',
        'source_file',
        'created_by',
        'updated_by',
        'slug',
    ];
}
