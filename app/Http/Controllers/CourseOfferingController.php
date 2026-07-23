<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseOffering;
use App\Http\Requests\StoreCourseOfferingRequest;
use App\Http\Requests\UpdateCourseOfferingRequest;
use App\Models\Level;
use App\Models\ProgrammeLevelSemester;
use App\Models\Semester;
use App\Models\StudentElective;
use Illuminate\Http\Request;
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

        $currentSemesterId = ProgrammeLevelSemester::where('programme_id', $user->programme_id)
            ->where('level_id', $user->level_id)
            ->value('semester_id');

        return Inertia::render('CourseOfferings/Index', [
            'courseOfferings' => CourseOffering::with(['course', 'level', 'semester'])
                ->where('programme_id', $user->programme_id)
                ->where('level_id', $user->level_id)
                ->when($currentSemesterId, fn($q) => $q->where('semester_id', $currentSemesterId))
                ->latest()
                ->get(),
            'courses' => Course::all(['id', 'code', 'title']),
            'levels' => Level::all(['id', 'name']),
            'semesters' => Semester::all(['id', 'name']),
        ]);
    }

    public function studentCoursesOfferings()
    {
        $user = Auth::user();

        $currentSemesterId = ProgrammeLevelSemester::where('programme_id', $user->programme_id)
            ->where('level_id', $user->level_id)
            ->value('semester_id');

        return Inertia::render('Courses/Index', [
            'courseOfferings' => CourseOffering::with('course')
                ->where('programme_id', $user->programme_id)
                ->where('level_id', $user->level_id)
                ->where('semester_id', $currentSemesterId)
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
        return Inertia::render('CourseOfferings/Create', [
            'courses' => Course::where('school_id', Auth::user()->school_id)
                ->get(['id', 'code', 'title']),
            'levels' => Level::all(['id', 'name']),
            'semesters' => Semester::all(['id', 'name']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeStudentElective(Request $request)
    {
        $data = $request->validate([
            'course_offering_id' => ['required', 'integer', 'exists:course_offerings,id'],
        ]);

        StudentElective::firstOrCreate([
            'student_id' => auth()->id(),
            'course_offering_id' => $data['course_offering_id'],
        ]);

//        return back();
    }

    public function store(StoreCourseOfferingRequest $request)
    {
        $data = $request->validated();
        CourseOffering::create([
            'course_id' => $data['course_id'],
            'level_id' => Auth::user()->level_id,
            'semester_id' => $data['semester_id'],
            'type' => $data['type'],
            'is_general' => $data['is_general'] ?? false,
            'programme_id' => Auth::user()->programme_id,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        return back()->with('success', 'Course offering added successfully.');
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
        abort_unless((int)$courseOffering->programme_id === (int)Auth::user()->programme_id, 403);

        $data = $request->validated();

        $courseOffering->update([
            'course_id' => $data['course_id'],
            'level_id' => Auth::user()->level_id,
            'semester_id' => $data['semester_id'],
            'type' => $data['type'],
            'is_general' => $data['is_general'] ?? false,
            'updated_by' => Auth::id(),
        ]);

        return back()->with('success', 'Course offering updated.');
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
