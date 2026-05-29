<?php

namespace App\Http\Controllers;

use App\Models\CourseOffering;
use App\Models\Programme;
use App\Models\StudentElective;
use App\Models\TimetableSlot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        // Core + general offerings
        $offeringCourseIds = CourseOffering::where('programme_id', $user->programme_id)
            ->where('level_id', $user->level_id)
            ->where('type', 'core')
            ->pluck('course_id');

        // Student electives
        $electiveOfferingIds = StudentElective::where('student_id', $user->id)
            ->pluck('course_offering_id');

        $electiveCourseIds = CourseOffering::whereIn('id', $electiveOfferingIds)
            ->pluck('course_id');

        // Merge all courses
        $courseIds = $offeringCourseIds
            ->merge($electiveCourseIds)
            ->unique();

        // Timetable
        $timetable = TimetableSlot::with('course')
            ->whereIn('course_id', $courseIds)
            ->get();

//        dd($timetable);

        return Inertia::render('Welcome', [
            'timetable' => $timetable,
            'programme' => Programme::where('id', $user->programme_id)->first(),
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
