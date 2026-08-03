<?php

namespace App\Services;

use App\Models\CourseOffering;
use App\Models\ProgrammeLevelSemester;
use App\Models\StudentElective;
use App\Models\User;
use Illuminate\Support\Collection;

class CourseScopeResolver
{
    /**
     * Course IDs relevant to a specific user for their CURRENT semester
     * (core + general + elective). Same logic as
     * CheckTimetableNotifications@getUserCourseIds — kept here as the
     * single source of truth so it isn't reimplemented (and drifted)
     * in more than one place.
     */
    public function activeCourseIdsForUser(User $user): Collection
    {
        $currentSemesterId = $this->currentSemesterId($user->programme_id, $user->level_id);

        if (! $currentSemesterId) {
            return collect();
        }

        $coreCourseIds = CourseOffering::where('programme_id', $user->programme_id)
            ->where('level_id', $user->level_id)
            ->where('semester_id', $currentSemesterId)
            ->where(function ($query) {
                $query->where('type', 'core')->orWhere('is_general', true);
            })
            ->pluck('course_id');

        $electiveOfferingIds = StudentElective::where('student_id', $user->id)
            ->whereHas('courseOffering', fn ($q) => $q->where('semester_id', $currentSemesterId))
            ->pluck('course_offering_id');

        $electiveCourseIds = CourseOffering::whereIn('id', $electiveOfferingIds)->pluck('course_id');

        return $coreCourseIds->merge($electiveCourseIds)->unique();
    }

    /**
     * Every course_id currently "active" system-wide — i.e. its
     * course_offerings.semester_id matches what ProgrammeLevelSemester
     * says is current for that programme+level right now. Used by
     * qotd:select to decide which courses need a Question of the Day.
     */
    public function allActiveCourseIds(): Collection
    {
        return CourseOffering::query()
            ->join('programme_level_semesters as pls', function ($join) {
                $join->on('pls.programme_id', '=', 'course_offerings.programme_id')
                    ->on('pls.level_id', '=', 'course_offerings.level_id');
            })
            ->whereColumn('course_offerings.semester_id', 'pls.semester_id')
            ->pluck('course_offerings.course_id')
            ->unique();
    }

    public function allActiveGeneralCourseIds(): Collection
    {
        return CourseOffering::query()
            ->join('programme_level_semesters as pls', function ($join) {
                $join->on('pls.programme_id', '=', 'course_offerings.programme_id')
                    ->on('pls.level_id', '=', 'course_offerings.level_id');
            })
            ->whereColumn('course_offerings.semester_id', 'pls.semester_id')
            ->where('course_offerings.is_general', true)
            ->pluck('course_offerings.course_id')
            ->unique();
    }

    private function currentSemesterId(?int $programmeId, ?int $levelId): ?int
    {
        if (! $programmeId || ! $levelId) {
            return null;
        }

        return ProgrammeLevelSemester::where('programme_id', $programmeId)
            ->where('level_id', $levelId)
            ->value('semester_id');
    }
}
