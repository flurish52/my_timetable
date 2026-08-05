<?php

namespace App\Console\Commands;

use App\Models\Question;
use App\Models\QuestionOfTheDay;
use App\Services\CourseScopeResolver;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class SelectQuestionsOfTheDay extends Command
{
    protected $signature = 'qotd:select';
    protected $description = 'Pick today\'s Question of the Day: one general question, plus one per active course';

    public function __construct(private CourseScopeResolver $courseScopeResolver)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $today = today();
        $lookbackDays = 30;

        $generalCourseIds = $this->courseScopeResolver->allActiveGeneralCourseIds();

        if ($generalCourseIds->isNotEmpty()) {
            // Pick ONE random course from the general pool — bounded by the
            // pool's actual size, so the index is never out of range.
            $randomIndex = random_int(0, $generalCourseIds->count() - 1);
            $pickedCourseId = $generalCourseIds->values()->get($randomIndex);

            // course_id is now always filled — even for 'general' scope — so we
            // can track which specific course each day's general question came from.
            $this->selectForScope('general', $pickedCourseId, collect([$pickedCourseId]), $today, $lookbackDays);
        } else {
            // Different scenario: no general courses exist at all right now.
            // Nothing to pick — skip today's general QOTD rather than error out.
            $this->warn('No general courses available — skipping general Question of the Day for today.');
        }

        $activeCourseIds = $this->courseScopeResolver->allActiveCourseIds();

        foreach ($activeCourseIds as $courseId) {
            $this->selectForScope('course', $courseId, collect([$courseId]), $today, $lookbackDays);
        }

        $this->info('QOTD selection complete for ' . $today->toDateString());

        return self::SUCCESS;
    }

    private function selectForScope(string $scopeType, ?int $courseId, Collection $poolCourseIds, Carbon $date, int $lookbackDays): void
    {
        if ($poolCourseIds->isEmpty()) {
            return;
        }

        $exists = QuestionOfTheDay::where('date', $date)
            ->where('scope_type', $scopeType)
            ->where('course_id', $courseId)
            ->exists();

        if ($exists) {
            return;
        }

        $recentlyUsedQuestionIds = QuestionOfTheDay::where('scope_type', $scopeType)
            ->where('course_id', $courseId)
            ->where('date', '>=', $date->copy()->subDays($lookbackDays))
            ->pluck('question_id');

        $question = $this->pickQuestion($poolCourseIds, $recentlyUsedQuestionIds);

        if (! $question) {
            $question = $this->pickQuestion($poolCourseIds, collect());
        }

        if ($question) {
            QuestionOfTheDay::create([
                'question_id' => $question->id,
                'date' => $date,
                'scope_type' => $scopeType,
                'course_id' => $courseId,
            ]);
        }
    }

    private function pickQuestion(Collection $poolCourseIds, Collection $excludeIds): ?Question
    {
        return Question::whereNotIn('id', $excludeIds)
            ->whereHas('pastQuestion', function ($q) use ($poolCourseIds) {
                $q->where('status', 'published')->whereIn('course_id', $poolCourseIds);
            })
            ->inRandomOrder()
            ->first();
    }
}
