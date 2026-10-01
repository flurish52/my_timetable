<?php

namespace App\Http\Controllers;

use App\Actions\BuildScannedPastQuestion;
use App\Contracts\NotesQuestionGeneratorContract;
use App\Exceptions\AiServiceRateLimitedException;
use App\Models\Course;
use App\Models\PastQuestion;
use App\Models\Question;
use App\Models\ScanAttempt;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Smalot\PdfParser\Parser as PdfParser;
use Throwable;



class NotesScanController extends Controller
{
    private const MIN_QUESTIONS = 10;
    private const MAX_QUESTIONS = 50;
    private const QUESTION_STEP = 10;
    public function __construct(
        private readonly NotesQuestionGeneratorContract $generator,
        private readonly BuildScannedPastQuestion $builder,
    ) {}

    public function review(PastQuestion $pastQuestion): Response
    {
        $user = auth()->user();

        abort_unless($pastQuestion->kind === 'notes_quiz', 404);
        abort_unless(
            (int) $pastQuestion->created_by === (int) $user->id || $user->hasRole('admin'),
            404
        );

        return Inertia::render('PracticePastQuestion/PracticePastQuestions', [
            'past_question' => $pastQuestion->load([
                'course:id,code,title',
                'questions' => fn ($q) => $q->orderBy('position'),
                'questions.options',
                'questions.answers',
            ]),
        ]);
    }

    public function updateQuestion(Request $request, Question $question): RedirectResponse
    {
        $this->authorizeQuestion($question);

        $data = $request->validate([
            'question_text' => ['required', 'string', 'max:2000'],
            'explanation' => ['nullable', 'string', 'max:1000'],
            'options' => ['nullable', 'array', 'max:6'],
            'options.*.id' => ['required', 'integer'],
            'options.*.option_text' => ['required', 'string', 'max:500'],
            'correct_option_id' => ['required_with:options', 'nullable', 'integer'],
            'answer_text' => ['nullable', 'string', 'max:2000'],
        ]);

        $ownIds = $question->options()->pluck('id')->all();

        if (! empty($data['options']) && ! in_array((int) $data['correct_option_id'], $ownIds, true)) {
            return back()->withErrors(['correct_option_id' => 'Pick one of the options as the correct answer.']);
        }

        DB::transaction(function () use ($question, $data, $ownIds) {
            $question->update([
                'question_text' => $data['question_text'],
                'explanation' => $data['explanation'] ?? null,
            ]);

            if (! empty($data['options'])) {
                foreach ($data['options'] as $opt) {
                    if (in_array((int) $opt['id'], $ownIds, true)) {
                        $question->options()->whereKey($opt['id'])->update(['option_text' => $opt['option_text']]);
                    }
                }

                $question->options()->update(['is_correct' => false]);
                $question->options()->whereKey($data['correct_option_id'])->update(['is_correct' => true]);
            }

            if ($question->question_type === 'short_answer' && filled($data['answer_text'] ?? null)) {
                $question->answers()->updateOrCreate([], ['answer_text' => $data['answer_text']]);
            }
        });

        return back();
    }

    public function destroyQuestion(Question $question): RedirectResponse
    {
        $pastQuestion = $this->authorizeQuestion($question);

        if ($pastQuestion->questions()->count() <= 1) {
            return back()->withErrors(['question' => 'A quiz needs at least one question.']);
        }

        DB::transaction(function () use ($question) {
            $question->attemptAnswers()->delete();
            $question->options()->delete();
            $question->answers()->delete();
            $question->delete();
        });

        return back();
    }

    private function authorizeQuestion(Question $question): PastQuestion
    {
        $pastQuestion = $question->pastQuestion;

        abort_unless(
            $pastQuestion
            && $pastQuestion->kind === 'notes_quiz'
            && (int) $pastQuestion->created_by === (int) auth()->id(),
            404
        );

        return $pastQuestion;
    }

    public function create(Request $request, ?string $courseId = null): Response
    {
        $user = $request->user();

        return Inertia::render('Scan/Notes', [
            'course' => $courseId ? Course::find($courseId, ['id', 'code', 'title']) : null,
            'courses' => $user?->programme_id
                ? Course::whereHas('course_offering', fn ($q) => $q->where('programme_id', $user->programme_id))
                    ->orderBy('title')->get(['id', 'code', 'title'])
                : [],
            'maxImages' => ScanController::MAX_IMAGES_PER_SCAN,
            'scansRemaining' => $user
                ? max(0, ScanController::DAILY_SCAN_LIMIT - $this->todaysScanCount($user->id))
                : null,
            'isGuest' => ! $user,
            'questionLimits' => [
                'min' => self::MIN_QUESTIONS,
                'max' => self::MAX_QUESTIONS,
                'step' => self::QUESTION_STEP,
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        set_time_limit(180);
        $validated = $request->validate([
            'images' => ['required', 'array', 'min:1', 'max:' . ScanController::MAX_IMAGES_PER_SCAN],
            'images.*' => ['file', 'mimes:jpeg,jpg,png,pdf', 'max:8192'],
            'course_id' => ['nullable', 'integer', 'exists:courses,id'],
            'question_count' => ['nullable', 'integer', 'min:' . self::MIN_QUESTIONS, 'max:' . self::MAX_QUESTIONS],            'types' => ['nullable', 'array', 'min:1'],
            'types.*' => ['in:objective,true_false,short_answer'],
            'difficulty' => ['nullable', 'in:easy,medium,hard'],
        ]);

        $options = [
            'count' => (int) ($validated['question_count'] ?? 10),
            'types' => array_values(array_unique($validated['types'] ?? ['objective'])),
            'difficulty' => $validated['difficulty'] ?? 'medium',
        ];

        $files = $request->file('images');
        $pdfCount = collect($files)->filter(fn ($f) => $f->getMimeType() === 'application/pdf')->count();

        if ($pdfCount > 0 && count($files) > 1) {
            return back()->withErrors([
                'images' => 'Upload either a single PDF or up to ' . ScanController::MAX_IMAGES_PER_SCAN . ' photos, not both.',
            ]);
        }

        if ($pdfCount === 1) {
            $pages = count((new PdfParser())->parseFile($files[0]->getRealPath())->getPages());

            if ($pages > ScanController::MAX_IMAGES_PER_SCAN) {
                return back()->withErrors([
                    'images' => "That PDF has {$pages} pages. Max " . ScanController::MAX_IMAGES_PER_SCAN . ' pages per scan.',
                ]);
            }
        }

        $user = $request->user();

        if ($this->todaysScanCount($user->id) >= ScanController::DAILY_SCAN_LIMIT) {
            return back()->withErrors(['images' => 'You have reached today\'s scan limit. Please try again tomorrow.']);
        }

        $paths = [];
        $fullPaths = [];

        foreach ($files as $file) {
            $path = $file->store('scans/' . $user->id, 'local');
            $paths[] = $path;
            $fullPaths[] = Storage::disk('local')->path($path);
        }

        $scanAttempt = ScanAttempt::create([
            'user_id' => $user->id,
            'type' => 'notes',
            'status' => 'pending',
            'file_paths' => $paths,
            'options' => $options,
        ]);

        try {
            $result = $this->generator->generate($fullPaths, $options);
        } catch (AiServiceRateLimitedException $e) {
            $scanAttempt->update(['status' => 'failed', 'rejection_reason' => 'Rate limited by AI provider.']);

            return back()->withErrors(['images' => 'We\'re seeing high traffic right now. Please wait a minute and try again.']);
        } catch (Throwable $e) {
            // Never surface $e->getMessage() to the user.
            Log::error('Notes scan generation failed', [
                'scan_attempt_id' => $scanAttempt->id,
                'exception' => get_class($e),
                'code' => $e->getCode(),
            ]);

            $scanAttempt->update(['status' => 'failed', 'rejection_reason' => 'Generation failed: ' . get_class($e)]);

            return back()->withErrors(['images' => 'We couldn\'t process your notes right now. Please try again in a moment.']);
        }

        if (! ($result['is_valid_notes'] ?? false)) {
            $scanAttempt->update([
                'status' => 'rejected',
                'rejection_reason' => $result['rejection_reason'] ?? 'Not recognized as study notes.',
                'raw_ai_response' => $result,
            ]);

            return back()->withErrors([
                'images' => $result['rejection_reason'] ?? 'This doesn\'t look like study notes. Try a clearer photo.',
            ]);
        }

        if (empty($result['questions'])) {
            $scanAttempt->update([
                'status' => 'failed',
                'rejection_reason' => 'No usable questions generated.',
                'raw_ai_response' => $result,
            ]);

            return back()->withErrors([
                'images' => 'We couldn\'t build questions from these notes. Try clearer photos or more content.',
            ]);
        }

        $courseId = $validated['course_id'] ?? null;

        try {
            $pastQuestion = DB::transaction(
                fn () => $this->builder->handle($result, $user->id, $courseId, 'notes_quiz')
            );
        } catch (Throwable $e) {
            Log::error('Failed to save notes quiz', [
                'scan_attempt_id' => $scanAttempt->id,
                'exception' => get_class($e),
            ]);

            $scanAttempt->update([
                'status' => 'failed',
                'rejection_reason' => 'Failed to save generated questions.',
                'raw_ai_response' => $result,
            ]);

            return back()->withErrors(['images' => 'Something went wrong saving your quiz. Please try again.']);
        }

        Storage::disk('local')->delete($paths);

        // raw_ai_response keeps notes_text, which a future "generate more" can reuse.
        $scanAttempt->update([
            'status' => 'success',
            'past_question_id' => $pastQuestion->id,
            'raw_ai_response' => $result,
            'file_paths' => null,
        ]);

        return redirect()
            ->route('scan.notes.review', $pastQuestion)
            ->with('status', 'Quiz generated. Review the questions below.');
    }


    private function todaysScanCount(int $userId): int
    {
        return ScanAttempt::where('user_id', $userId)
            ->whereDate('created_at', today())
            ->whereIn('status', ['success', 'rejected'])
            ->count();
    }
}
