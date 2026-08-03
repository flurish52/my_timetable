<?php

namespace App\Http\Controllers;

use App\Models\QuestionOfTheDay;
use App\Models\QuestionOfTheDayAttempt;
use App\Models\QuestionOption;
use App\Services\QotdResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class QuestionOfTheDayController extends Controller
{
    public function __construct(private QotdResolver $qotdResolver)
    {
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $today = today();

        $qotd = $this->qotdResolver->resolveForUser($user, $today);

        $myAttempt = $qotd
            ? QuestionOfTheDayAttempt::where('question_of_the_day_id', $qotd->id)
                ->where('user_id', $user->id)
                ->first()
            : null;

        return Inertia::render('QuestionOfTheDay/Index', [
            'question' => $qotd?->load([
                'question.options',
                'question.answers',
                'question.pastQuestion.school',
                'question.pastQuestion.semester',
            ]),
            'attempt' => $myAttempt,
            'streak' => [
                'current' => $user->qotd_current_streak,
                'longest' => $user->qotd_longest_streak,
            ],
        ]);
    }

    public function attempt(Request $request, QuestionOfTheDay $questionOfTheDay)
    {
        $isMcq = in_array($questionOfTheDay->question->question_type, ['objective', 'true_false']);

        $validated = $request->validate([
            'answer_text' => $isMcq ? 'nullable|string' : 'required|string|min:1',
            'selected_option_id' => $isMcq
                ? 'required|integer|exists:question_options,id'
                : 'nullable|integer',
        ]);

        $user = $request->user();

        $alreadyAttempted = QuestionOfTheDayAttempt::where('question_of_the_day_id', $questionOfTheDay->id)
            ->where('user_id', $user->id)
            ->exists();

        if ($alreadyAttempted) {
            return back()->withErrors(['answer' => 'You already attempted today\'s question.']);
        }

        $isCorrect = $this->checkCorrectness($questionOfTheDay, $validated);

        DB::transaction(function () use ($questionOfTheDay, $user, $validated, $isCorrect) {
            QuestionOfTheDayAttempt::create([
                'question_of_the_day_id' => $questionOfTheDay->id,
                'user_id' => $user->id,
                'answer_text' => $validated['answer_text'] ?? null,
                'selected_option_id' => $validated['selected_option_id'] ?? null,
                'is_correct' => $isCorrect,
                'attempted_at' => now(),
            ]);

            $this->updateStreak($user);
        });

        return back();
    }

    public function markShared(Request $request, QuestionOfTheDay $questionOfTheDay)
    {
        $validated = $request->validate(['with_answer' => 'required|boolean']);

        $user = $request->user();

        $existingAttempt = QuestionOfTheDayAttempt::where('question_of_the_day_id', $questionOfTheDay->id)
            ->where('user_id', $user->id)
            ->first();

        // Can't share "with answer" if there's no answer to share
        if ($validated['with_answer'] && !$existingAttempt) {
            return back()->withErrors(['share' => 'Answer the question first to share your answer.']);
        }

        QuestionOfTheDayAttempt::updateOrCreate(
            ['question_of_the_day_id' => $questionOfTheDay->id, 'user_id' => $user->id],
            [
                'shared' => true,
                'shared_with_answer' => $validated['with_answer'],
                'attempted_at' => $existingAttempt->attempted_at ?? now(),
            ]
        );

        return back();
    }

    private function checkCorrectness(QuestionOfTheDay $qotd, array $validated): ?bool
    {
        $question = $qotd->question;

        if (! in_array($question->question_type, ['objective', 'true_false'])) {
            return null;
        }

        if (empty($validated['selected_option_id'])) {
            return false;
        }

        return QuestionOption::where('id', $validated['selected_option_id'])
            ->where('question_id', $question->id)
            ->where('is_correct', true)
            ->exists();
    }

    private function updateStreak($user): void
    {
        $today = today();
        $yesterday = today()->subDay();

        if ($user->qotd_last_attempted_date?->isSameDay($yesterday)) {
            $user->qotd_current_streak += 1;
        } elseif (! $user->qotd_last_attempted_date?->isSameDay($today)) {
            $user->qotd_current_streak = 1;
        }

        $user->qotd_longest_streak = max($user->qotd_longest_streak, $user->qotd_current_streak);
        $user->qotd_last_attempted_date = $today;
        $user->save();
    }
}
