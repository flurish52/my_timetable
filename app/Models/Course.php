<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    /** @use HasFactory<\Database\Factories\CourseFactory> */
    use HasFactory;

    public function timetables()
    {
        return $this->hasMany(Timetable::class);
    }

    public function course_offering()
    {
        return $this->hasMany(CourseOffering::class);
    }
    public function past_question()
    {
        return $this->hasMany(PastQuestion::class);
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
