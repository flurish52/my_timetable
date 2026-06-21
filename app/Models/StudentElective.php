<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentElective extends Model
{
    public function courseOffering()
    {
        return $this->belongsTo(CourseOffering::class);
    }

    protected $fillable = [
        'student_id',
        'course_offering_id',
    ];
}
