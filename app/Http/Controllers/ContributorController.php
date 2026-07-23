<?php

namespace App\Http\Controllers;

use App\Models\CourseOffering;
use App\Models\PastQuestion;
use App\Models\TimetableSlot;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ContributorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
// ContributorController.php
    public function index()
    {
        $schoolId = auth()->user()->school_id;

        $contributors = User::whereHas('pastQuestions', function ($q) use ($schoolId) {
            $q->where('school_id', $schoolId)->where('status', 'published');
        })
            ->withCount(['pastQuestions' => function ($q) use ($schoolId) {
                $q->where('school_id', $schoolId)->where('status', 'published');
            }])
            ->orderByDesc('past_questions_count')
            ->get()
            ->map(fn ($u) => [
                'id' => $u->id,
                'username' => $u->username,
                'year' => $u->level->name ?? null,
                'contributions_count' => $u->past_questions_count,
            ]);

        return Inertia::render('Public/Contributors', [
            'contributors' => $contributors,
            'school' => auth()->user()->school->only(['id', 'name']),
        ]);
    }




    public function dashboard()
    {
        $user = Auth::user();

        $courseOfferings = CourseOffering::where('programme_id', $user->programme_id)
            ->where('level_id', $user->level_id)
            ->with('course')
            ->get();

        $courseIds = $courseOfferings->pluck('course_id')->unique();

        $timetableCount = TimetableSlot::whereIn('course_id', $courseIds)->count();

        $pastQuestions = PastQuestion::whereIn('course_id', $courseIds)->where('created_by', $user->id)->get();

        $draftPastQuestions = $pastQuestions->where('status', 'draft')->values();

        $coursesWithoutPastQuestions = $courseOfferings->pluck('course')
            ->unique('id')
            ->reject(fn ($course) => $pastQuestions->contains('course_id', $course->id))
            ->values();

        $peerContributorsCount = User::role('contributor')
            ->where('programme_id', $user->programme_id)
            ->where('level_id', $user->level_id)
            ->where('id', '!=', $user->id)
            ->count();

        return Inertia::render('Contributor/Dashboard', [
            'stats' => [
                'course_offerings' => $courseOfferings->count(),
                'timetable_slots' => $timetableCount,
                'past_questions_published' => $pastQuestions->where('status', 'published')->count(),
                'past_questions_draft' => $draftPastQuestions->count(),
            ],
            'draftPastQuestions' => $draftPastQuestions->map(fn ($pq) => [
                'id' => $pq->id,
                'title' => $pq->title,
            ])->values(),
            'coursesWithoutPastQuestions' => $coursesWithoutPastQuestions->map(fn ($c) => [
                'id' => $c->id,
                'code' => $c->code,
                'title' => $c->title,
            ])->values(),
            'peerContributorsCount' => $peerContributorsCount,
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
