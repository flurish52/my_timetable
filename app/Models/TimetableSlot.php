<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimetableSlot extends Model
{
    /** @use HasFactory<\Database\Factories\TimetableSlotFactory> */
    use HasFactory;

    protected $table = 'timetable';


    protected $fillable = [
        'programme_id',
        'course_id',
        'day_of_week',
        'start_time',
        'end_time',
        'venue',
        'lecturer',
        'is_elective_slot',
    ];

    public function programme()
    {
        return $this->belongsTo(Programme::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function level()
    {
        return $this->belongsTo(Level::class);
    }

}
