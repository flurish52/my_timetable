<?php

namespace App\Http\Controllers;

use App\Actions\BuildScannedPastQuestion;
use App\Contracts\QuestionExtractorContract;
use App\Exceptions\AiServiceRateLimitedException;
use App\Models\Course;
use App\Models\PastQuestion;
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

class ScanController extends Controller
{
    public const DAILY_SCAN_LIMIT = 3;

    public const MAX_IMAGES_PER_SCAN = 10;

    public function __construct(
        private readonly QuestionExtractorContract $extractor,
        private readonly BuildScannedPastQuestion $builder,
    ) {
    }

    public function create(Request $request, ?string $courseId = null): Response
    {
        $course = $courseId ? Course::find($courseId) : null;

        return Inertia::render('Scan/Create', [
            'course' => $course,
            'maxImages' => self::MAX_IMAGES_PER_SCAN,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'images' => ['required', 'array', 'min:1', 'max:' . self::MAX_IMAGES_PER_SCAN],
            'images.*' => ['file', 'mimes:jpeg,jpg,png,pdf', 'max:8192'],
            'course_id' => ['nullable', 'integer', 'exists:courses,id'],
        ]);

        $files = $request->file('images');
        $pdfCount = collect($files)->filter(fn ($f) => $f->getMimeType() === 'application/pdf')->count();

        // A PDF replaces the whole batch: no mixing PDF + photos, and only one PDF at a time.
        if ($pdfCount > 0 && count($files) > 1) {
            return back()->withErrors([
                'images' => 'Upload either a single PDF or up to ' . self::MAX_IMAGES_PER_SCAN . ' photos, not both.',
            ]);
        }

        // PDF page count is the real cost driver, so check it before touching Gemini at all.
        if ($pdfCount === 1) {
            $pages = count((new PdfParser())->parseFile($files[0]->getRealPath())->getPages());

            if ($pages > self::MAX_IMAGES_PER_SCAN) {
                return back()->withErrors([
                    'images' => "That PDF has {$pages} pages. Max " . self::MAX_IMAGES_PER_SCAN . ' pages per scan.',
                ]);
            }
        }

        $user = $request->user();

        // Notes scans and paper scans share one daily limit (same AI cost).
        $todaysScanCount = ScanAttempt::where('user_id', $user->id)
            ->whereDate('created_at', today())
            ->whereIn('status', ['success', 'rejected'])
            ->count();

        if ($todaysScanCount >= self::DAILY_SCAN_LIMIT) {
            return back()->withErrors([
                'images' => 'You have reached today\'s scan limit. Please try again tomorrow.',
            ]);
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
            'type' => 'paper',
            'status' => 'pending',
            'file_paths' => $paths,
        ]);

        try {
            $result = $this->extractor->extract($fullPaths);
        } catch (AiServiceRateLimitedException $e) {
            Log::warning('Scan extraction rate limited', ['scan_attempt_id' => $scanAttempt->id]);

            $scanAttempt->update([
                'status' => 'failed',
                'rejection_reason' => 'Rate limited by AI provider.',
            ]);

            return back()->withErrors([
                'images' => 'We\'re seeing high traffic right now. Please wait a minute and try scanning again.',
            ]);
        } catch (Throwable $e) {
            // Log the real error internally, but NEVER expose $e->getMessage() to the user.
            Log::error('Scan extraction failed', [
                'scan_attempt_id' => $scanAttempt->id,
                'exception' => get_class($e),
                'code' => $e->getCode(),
            ]);

            $scanAttempt->update([
                'status' => 'failed',
                'rejection_reason' => 'Extraction failed: ' . get_class($e),
            ]);

            // Files stay on disk for the 24-48h retry window per policy;
            // the scheduled cleanup command purges them later regardless of status.
            return back()->withErrors([
                'images' => 'We couldn\'t process your scan right now. Please try again in a moment.',
            ]);
        }

        $isInvalid = ! $result['is_valid_question_paper'];
        $isMixedPaper = ! ($result['is_single_paper'] ?? true);

        if ($isInvalid || $isMixedPaper) {
            $scanAttempt->update([
                'status' => 'rejected',
                'rejection_reason' => $result['rejection_reason'] ?? ($isMixedPaper
                        ? 'These pages appear to belong to more than one paper. Please scan one paper at a time.'
                        : 'Not recognized as a question paper.'),
                'raw_ai_response' => $result,
            ]);

            return back()->withErrors([
                'images' => $result['rejection_reason'] ?? 'This doesn\'t look like a single question paper. Try again with pages from one paper only.',
            ]);
        }

        $courseId = $request->integer('course_id') ?: null;

        try {
            $pastQuestion = DB::transaction(
                fn () => $this->builder->handle($result, $user->id, $courseId, 'past_question')
            );
        } catch (Throwable $e) {
            Log::error('Failed to save extracted past question', [
                'scan_attempt_id' => $scanAttempt->id,
                'exception' => get_class($e),
            ]);

            $scanAttempt->update([
                'status' => 'failed',
                'rejection_reason' => 'Failed to save extracted questions.',
                'raw_ai_response' => $result,
            ]);

            return back()->withErrors(['images' => 'Something went wrong saving your paper. Please try again.']);
        }

        // Success: delete the temp images immediately per our retention policy.
        Storage::disk('local')->delete($paths);

        $scanAttempt->update([
            'status' => 'success',
            'past_question_id' => $pastQuestion->id,
            'raw_ai_response' => $result,
            'file_paths' => null,
        ]);

        if ($courseId) {
            $course = Course::find($courseId);

            return redirect()
                ->route('view.start_practice', [
                    'slug' => $course->code,
                    'question_slug' => $pastQuestion->id,
                ])
                ->with('status', 'Paper scanned successfully. Review your questions below.');
        }

        return redirect()
            ->route('scan.review', $pastQuestion)
            ->with('status', 'Paper scanned successfully. Review your questions below.');
    }

    public function review(PastQuestion $pastQuestion): Response
    {
        $user = auth()->user();

        abort_unless(
            (int) $pastQuestion->created_by === (int) $user->id || $user->hasRole('admin'),
            404
        );

        return Inertia::render('PracticePastQuestion/PracticePastQuestions', [
            'past_question' => $pastQuestion->load(
                'course', 'semester', 'school', 'sections', 'questions', 'creator', 'updater'
            ),
        ]);
    }
}
