<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Programme extends Model
{
    /** @use HasFactory<\Database\Factories\ProgrammeFactory> */
    use HasFactory;

    public function timetables()
    {
        return $this->hasMany(Timetable::class);
    }
    public function programme_type()
    {
        return $this->belongsTo(ProgrammeType::class);
    }
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    protected $fillable = [
        'department_id',
        'programme_type_id',
        'name',
    ];
}
