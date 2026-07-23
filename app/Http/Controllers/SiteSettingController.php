<?php

namespace App\Http\Controllers;

use App\Models\Level;
use App\Models\Programme;
use App\Models\ProgrammeLevelSemester;
use App\Models\Semester;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SiteSettingController extends Controller
{
    /**
     * ADMIN — full access: pick any programme + level, assign a semester.
     */
    public function adminIndex(Request $request)
    {
        return Inertia::render('Settings/Index', [
            'role' => 'admin',
            'programmes' => Programme::orderBy('name')->get(['id', 'name']),
            'levels' => Level::orderBy('position')->get(['id', 'name']),
            'semesters' => Semester::all(['id', 'name']),
            'assignments' => ProgrammeLevelSemester::with(['programme:id,name', 'level:id,name', 'semester:id,name'])
                ->OrderBy('updated_at', 'DESC')
                ->get(),
        ]);
    }

    public function adminUpdateCurrentSemester(Request $request)
    {
        $validated = $request->validate([
            'programme_id' => ['required', 'exists:programmes,id'],
            'level_id' => ['required', 'exists:levels,id'],
            'semester_id' => ['required', 'exists:semesters,id'],
        ]);

        ProgrammeLevelSemester::updateOrCreate(
            [
                'programme_id' => $validated['programme_id'],
                'level_id' => $validated['level_id'],
            ],
            [
                'semester_id' => $validated['semester_id'],
                'updated_by' => $request->user()->id,
            ],
        );

        return back()->with('success', 'Semester updated.');
    }

    /**
     * CONTRIBUTOR — scoped: only their own programme + level, from auth().
     */
    public function contributorIndex(Request $request)
    {
        $user = $request->user();

        $current = ProgrammeLevelSemester::where('programme_id', $user->programme_id)
            ->where('level_id', $user->level_id)
            ->value('semester_id');

        return Inertia::render('Settings/Index', [
            'role' => 'contributor',
            'level' => $user->level()->first(['id', 'name']),
            'levels' => Level::all(['id', 'name']),
            'programme' => $user->programme()->first(['id', 'name']),
            'semesters' => Semester::all(['id', 'name']),
            'currentSemesterId' => $current,
        ]);
    }

    public function contributorUpdateCurrentSemester(Request $request)
    {
        $validated = $request->validate([
            'level_id' => ['required', 'exists:levels,id'],
            'semester_id' => ['required', 'exists:semesters,id'],
        ]);

        $user = $request->user();

        if ($validated['level_id'] !== $user->level_id) {
            $user->update(['level_id' => $validated['level_id']]);
        }

        ProgrammeLevelSemester::updateOrCreate(
            [
                'programme_id' => $user->programme_id,
                'level_id' => $validated['level_id'],
            ],
            [
                'semester_id' => $validated['semester_id'],
                'updated_by' => $user->id,
            ],
        );

        return back()->with('success', 'Level and semester updated.');
    }
}
