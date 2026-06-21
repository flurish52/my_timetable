<?php

namespace App\Http\Controllers;

use App\Models\CourseOffering;
use App\Models\StudentElective;
use App\Models\TimeTable;
use App\Http\Requests\StoreTimeTableRequest;
use App\Http\Requests\UpdateTimeTableRequest;
use App\Models\TimetableSlot;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TimeTableController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $baseOfferings = CourseOffering::where('programme_id', $user->programme_id)
            ->where('level_id', $user->level_id)
            ->where('type', 'core')
            ->get();
        $electives = StudentElective::where('student_id', $user->id)
            ->pluck('course_offering_id');
        $allowedOfferingIds = $baseOfferings->pluck('id')
            ->merge($electives)
            ->unique();
        $courseIds = CourseOffering::whereIn('id', $allowedOfferingIds)
            ->pluck('course_id');
        $timetable = TimetableSlot::with('course')
            ->whereIn('course_id', $courseIds)
            ->get();
        return inertia::render('FullTimeTable', [
            'timetable' => $timetable,
            'user' => $user,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTimeTableRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(TimeTable $timeTable)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TimeTable $timeTable)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTimeTableRequest $request, TimeTable $timeTable)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TimeTable $timeTable)
    {
        //
    }
}
