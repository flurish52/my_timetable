<?php

namespace App\Http\Controllers;

use App\Models\CourseOffering;
use App\Models\Programme;
use App\Models\ProgrammeLevelSemester;
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
        $isPwa = request()->query('source') === 'pwa';

        // Browser, not logged in → show the marketing landing page
        if (!$isPwa && !auth()->check()) {
            return Inertia::render('Welcome', []);
        }

        // PWA, not logged in → skip landing page, go straight to login
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if ($user->hasAnyRole(['admin', 'student', 'contributor']) && $user->profileSetupComplete) {
            return redirect()->route('dashboard');
        }

        return redirect()->route('activity.index');
    }


    public function dashboard()
    {
        $user = Auth::user();

        if ($user->roles->pluck('name')->sole() === 'independent') {
            return redirect('/activity');
        }

        $currentSemesterId = ProgrammeLevelSemester::where('programme_id', $user->programme_id)
            ->where('level_id', $user->level_id)
            ->value('semester_id');

        if (! $currentSemesterId) {
            return Inertia::render('Welcome', [
                'timetable' => [],
                'programme' => Programme::find($user->programme_id),
                'user' => $user,
                'noSemesterSet' => true,
            ]);
        }

        // Core + general offerings
        $offeringCourseIds = CourseOffering::where('programme_id', $user->programme_id)
            ->where('level_id', $user->level_id)
            ->where('semester_id', $currentSemesterId)
            ->where('type', 'core')
            ->pluck('course_id');

        // Student electives, scoped to the same semester
        $electiveOfferingIds = StudentElective::where('student_id', $user->id)
            ->whereHas('courseOffering', function ($q) use ($currentSemesterId, $user) {
                $q->where('semester_id', $currentSemesterId)
                    ->where('programme_id', $user->programme_id)
                    ->where('level_id', $user->level_id);
            })
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

        return Inertia::render('Dashboard', [
            'timetable' => $timetable,
            'programme' => Programme::find($user->programme_id),
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
