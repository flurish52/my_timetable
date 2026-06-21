<?php

namespace App\Http\Controllers;

use App\Models\CourseOffering;
use App\Http\Requests\StoreCourseOfferingRequest;
use App\Http\Requests\UpdateCourseOfferingRequest;
use App\Models\StudentElective;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CourseOfferingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
       return inertia::render('Courses/Index', [
            'courseOfferings' => CourseOffering::with('course')
                ->where('programme_id', $user->programme_id)
            ->where('level_id', $user->level_id)
                ->get(),

           'studentElectives' => $user->electives()
               ->select('id', 'course_offering_id')->get()
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
    public function store(StoreCourseOfferingRequest $request)
    {
        $data = $request->validate([
            'course_offering_id' => ['required', 'integer', 'exists:course_offerings,id'],
        ]);

        StudentElective::firstOrCreate([
            'student_id' => auth()->id(),
            'course_offering_id' => $data['course_offering_id'],
        ]);

        return back();
    }

    /**
     * Display the specified resource.
     */
    public function show(CourseOffering $courseOffering)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CourseOffering $courseOffering)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCourseOfferingRequest $request, CourseOffering $courseOffering)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CourseOffering $courseOffering)
    {
        StudentElective::where('student_id', Auth::id())
            ->where('course_offering_id', $courseOffering->id)
            ->delete();

        return redirect()->back();
    }
}
