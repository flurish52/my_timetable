<?php

namespace App\Services;

use App\Models\QuestionOfTheDay;
use App\Models\User;
use Carbon\Carbon;

class QotdResolver
{
    public function __construct(private CourseScopeResolver $courseScopeResolver)
    {
    }

    public function resolveForUser(User $user, Carbon $date): ?QuestionOfTheDay
    {
        $courseIds = $this->courseScopeResolver->activeCourseIdsForUser($user);

        if ($courseIds->isNotEmpty()) {
            $courseQotd = QuestionOfTheDay::where('date', $date)
                ->where('scope_type', 'course')
                ->whereIn('course_id', $courseIds)
                ->orderBy('course_id') // deterministic — same result on every call/reload
                ->first();

            if ($courseQotd) {
                return $courseQotd;
            }
        }

        return QuestionOfTheDay::where('date', $date)
            ->where('scope_type', 'general')
            ->first();
    }
}
