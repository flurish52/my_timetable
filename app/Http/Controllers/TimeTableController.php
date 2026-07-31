<?php

namespace App\Http\Controllers;

use App\Models\CourseOffering;
use App\Models\ProgrammeLevelSemester;
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

        $currentSemesterId = ProgrammeLevelSemester::where('programme_id', $user->programme_id)
            ->where('level_id', $user->level_id)
            ->value('semester_id');

        if (! $currentSemesterId) {
            return Inertia::render('Timetable/Index', [
                'timetable' => [],
                'courses' => [],
                'noSemesterSet' => true,
            ]);
        }

        $baseOfferings = CourseOffering::where('programme_id', $user->programme_id)
            ->where('level_id', $user->level_id)
            ->where('semester_id', $currentSemesterId)
            ->where('type', 'core')
            ->get();

        $electiveOfferingIds = StudentElective::where('student_id', $user->id)
            ->whereHas('courseOffering', function ($q) use ($currentSemesterId) {
                $q->where('semester_id', $currentSemesterId);
            })
            ->pluck('course_offering_id');

        $allowedOfferingIds = $baseOfferings->pluck('id')
            ->merge($electiveOfferingIds)
            ->unique();

        $courses = CourseOffering::whereIn('id', $allowedOfferingIds)
            ->with('course')
            ->get()
            ->pluck('course')
            ->unique('id')
            ->values();

        $timetable = TimetableSlot::with('course')
            ->whereIn('course_id', $courses->pluck('id'))
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        return Inertia::render('Timetable/Index', [
            'timetable' => $timetable,
            'courses' => $courses,
        ]);
    }

    public function viewFullTimetable()
    {
        $user = Auth::user();

        // Resolve the current semester for this student's programme + level
        $currentSemesterId = ProgrammeLevelSemester::where('programme_id', $user->programme_id)
            ->where('level_id', $user->level_id)
            ->value('semester_id');

        if (! $currentSemesterId) {
            return Inertia::render('FullTimeTable', [
                'timetable' => [],
                'user' => $user,
                'noSemesterSet' => true,
            ]);
        }

        $baseOfferings = CourseOffering::where('programme_id', $user->programme_id)
            ->where('level_id', $user->level_id)
            ->where('semester_id', $currentSemesterId)
            ->where('type', 'core')
            ->get();

        $electiveOfferingIds = StudentElective::where('student_id', $user->id)
            ->whereHas('courseOffering', function ($q) use ($currentSemesterId) {
                $q->where('semester_id', $currentSemesterId);
            })
            ->pluck('course_offering_id');

        $allowedOfferingIds = $baseOfferings->pluck('id')
            ->merge($electiveOfferingIds)
            ->unique();

        $courseIds = CourseOffering::whereIn('id', $allowedOfferingIds)
            ->pluck('course_id');

        $timetable = TimetableSlot::with('course')
            ->whereIn('course_id', $courseIds)
            ->get();

        return Inertia::render('FullTimeTable', [
            'timetable' => $timetable,
            'user' => $user,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();

        $currentSemesterId = ProgrammeLevelSemester::where('programme_id', $user->programme_id)
            ->where('level_id', $user->level_id)
            ->value('semester_id');

        if (! $currentSemesterId) {
            return Inertia::render('Timetable/Create', [
                'courses' => [],
                'noSemesterSet' => true,
            ]);
        }
        $courses = CourseOffering::where('programme_id', $user->programme_id)
            ->where('level_id', $user->level_id)
            ->when($currentSemesterId, fn ($q) => $q->where('semester_id', $currentSemesterId))
            ->with('course')
            ->get()
            ->pluck('course')
            ->unique('id')
            ->values();

        return Inertia::render('Timetable/Create', [
            'courses' => $courses,
            'noSemesterSet' => ! $currentSemesterId,
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTimeTableRequest $request)
    {
        $this->info('starting');
        $data = $request->validated();

        if (! $request->boolean('confirmed')) {
            $clash = $this->venueClash($data['venue'], $data['day_of_week'], $data['start_time'], $data['end_time']);

            if ($clash) {
                return back()->with('venueWarning', $this->clashMessage($clash))->withInput();
            }
        }

        TimetableSlot::create([
            'school_id' => Auth::user()->school_id,
            'course_id' => $data['course_id'],
            'day_of_week' => $data['day_of_week'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'venue' => $data['venue'],
            'lecturer' => $data['lecturer'] ?? null,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        return back()->with('success', 'Timetable slot added.');
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
    public function update(UpdateTimeTableRequest $request, TimetableSlot $timetable)
    {
        $data = $request->validated();

        if (! $request->boolean('confirmed')) {
            $clash = $this->venueClash($data['venue'], $data['day_of_week'], $data['start_time'], $data['end_time'], $timetable->id);

            if ($clash) {
                return back()->with('venueWarning', $this->clashMessage($clash))->withInput();
            }
        }

        $timetable->update([
            'course_id' => $data['course_id'],
            'day_of_week' => $data['day_of_week'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'venue' => $data['venue'],
            'lecturer' => $data['lecturer'] ?? null,
            'updated_by' => Auth::id(),
        ]);

        return back()->with('success', 'Timetable slot updated.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TimeTable $timeTable)
    {
        //
    }


    private function venueClash(string $venue, string $day, string $start, string $end, int $semesterId, ?int $ignoreId = null)
    {
        return TimetableSlot::with('course')
            ->where('school_id', Auth::user()->school_id)
            ->where('day_of_week', $day)
            ->where('venue', $venue)
            ->where('start_time', '<', $end)
            ->where('end_time', '>', $start)
            ->whereHas('course.courseOfferings', function ($q) use ($semesterId) {
                $q->where('semester_id', $semesterId);
            })
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->first();
    }

    private function clashMessage(TimetableSlot $slot): string
    {
        return "{$slot->course->code} is already scheduled at {$slot->venue} on "
            . ucfirst($slot->day_of_week)
            . " from {$slot->start_time} to {$slot->end_time}. Add yours here too, or pick a different venue?";
    }
}
