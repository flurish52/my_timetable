<?php

namespace App\Http\Controllers;

use App\Models\Level;
use App\Models\Programme;
use App\Models\ProgrammeLevelSemester;
use App\Models\Semester;
use Illuminate\Http\Request;

class ProgrammeLevelSemesterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $programmeId = $request->user()->programme_id;

        $levels = Level::orderBy('position')->get();
        $semesters = Semester::all();

        $current = ProgrammeLevelSemester::where('programme_id', $programmeId)
            ->pluck('semester_id', 'level_id');

        return response()->json([
            'levels' => $levels,
            'semesters' => $semesters,
            'current' => $current,
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
    public function show(ProgrammeLevelSemester $programmeLevelSemester)
    {
        //
    }


    public function contributorShow(Request $request)
    {
        $user = $request->user();

        $record = ProgrammeLevelSemester::where('programme_id', $user->programme_id)
            ->where('level_id', $user->level_id)
            ->first();

        return response()->json([
            'level' => $user->level->only(['id', 'name']),
            'semesters' => Semester::all(['id', 'name']),
            'current_semester_id' => $record?->semester_id,
        ]);
    }

    public function contributorUpdate(Request $request)
    {
        $validated = $request->validate([
            'semester_id' => ['required', 'exists:semesters,id'],
        ]);

        $user = $request->user();

        ProgrammeLevelSemester::updateOrCreate(
            [
                'programme_id' => $user->programme_id,
                'level_id' => $user->level_id,
            ],
            [
                'semester_id' => $validated['semester_id'],
                'updated_by' => $user->id,
            ],
        );

        return response()->json(['status' => 'ok']);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProgrammeLevelSemester $programmeLevelSemester)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'level_id' => ['required', 'exists:levels,id'],
            'semester_id' => ['required', 'exists:semesters,id'],
        ]);

        ProgrammeLevelSemester::updateOrCreate(
            [
                'programme_id' => $request->user()->programme_id,
                'level_id' => $validated['level_id'],
            ],
            [
                'semester_id' => $validated['semester_id'],
                'updated_by' => $request->user()->id,
            ],
        );

        return response()->json(['status' => 'ok']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProgrammeLevelSemester $programmeLevelSemester)
    {
        //
    }
}
