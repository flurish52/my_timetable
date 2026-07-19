<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseOffering extends Model
{
    /** @use HasFactory<\Database\Factories\CourseOfferingFactory> */
    use HasFactory;

    protected $fillable = [
        'course_id',
        'level_id',
        'semester_id',
        'type',
        'is_general',
        'programme_id',
        'created_by',
        'updated_by',
    ];

    function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    public function level()
    {
        return $this->belongsTo(Level::class, 'level_id');
    }
    public function semester()
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }
}
