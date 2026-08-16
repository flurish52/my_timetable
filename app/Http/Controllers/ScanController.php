<?php

namespace App\Http\Controllers;

use App\Contracts\QuestionExtractorContract;
use App\Models\Course;
use App\Models\PastQuestion;
use App\Models\Question;
use App\Models\QuestionAnswer;
use App\Models\QuestionOption;
use App\Models\QuestionSection;
use App\Models\ScanAttempt;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;
use Smalot\PdfParser\Parser as PdfParser;


class ScanController extends Controller
{
    private const DAILY_SCAN_LIMIT = 5;

    private const MAX_IMAGES_PER_SCAN = 6;

    public function __construct(
        private readonly QuestionExtractorContract $extractor
    ) {}

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
            'images' => ['required', 'array', 'min:1', 'max:'.self::MAX_IMAGES_PER_SCAN],
            'images.*' => ['file', 'mimes:jpeg,jpg,png,pdf', 'max:8192'],
            'course_id' => ['nullable', 'integer', 'exists:courses,id'],
        ]);

        $files = $request->file('images');
        $pdfCount = collect($files)->filter(fn ($f) => $f->getClientMimeType() === 'application/pdf')->count();

        // A PDF replaces the whole batch — no mixing PDF + photos, and only one PDF at a time.
        if ($pdfCount > 0 && count($files) > 1) {
            return back()->withErrors([
                'images' => 'Upload either a single PDF or up to '.self::MAX_IMAGES_PER_SCAN.' photos — not both.',
            ]);
        }

        // PDF page count is the real cost driver — check it before touching Gemini at all.
        if ($pdfCount === 1) {
            $pageCount = (new PdfParser())->parseFile($files[0]->getRealPath())->getPages();

            if (count($pageCount) > self::MAX_IMAGES_PER_SCAN) {
                return back()->withErrors([
                    'images' => 'That PDF has '.count($pageCount).' pages — max '.self::MAX_IMAGES_PER_SCAN.' pages per scan.',
                ]);
            }
        }

        $user = $request->user();

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

        foreach ($request->file('images') as $file) {
            $path = $file->store('scans/'.$user->id, 'local');
            $paths[] = $path;
            $fullPaths[] = Storage::disk('local')->path($path);
        }

        $scanAttempt = ScanAttempt::create([
            'user_id' => $user->id,
            'status' => 'pending',
            'file_paths' => $paths,
        ]);

        try {
            $result = $this->extractor->extract($fullPaths);
        } catch (Throwable $e) {
            // Log the real error internally, but NEVER expose $e->getMessage()
            // to the user — for HTTP client exceptions (e.g. cURL/connection
            // errors calling Gemini) the message contains the full request
            // URL, including the API key in the query string. Only safe,
            // non-sensitive details go into rejection_reason and the flash
            // error shown to the user.
            Log::error('Scan extraction failed', [
                'scan_attempt_id' => $scanAttempt->id,
                'exception' => get_class($e),
                'code' => $e->getCode(),
            ]);

            $scanAttempt->update([
                'status' => 'failed',
                'rejection_reason' => 'Extraction failed: '.get_class($e),
            ]);

            // Files stay on disk for the 24-48h retry window per policy —
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
            $pastQuestion = DB::transaction(function () use ($result, $courseId, $user) {
                return $this->buildPastQuestion($result, $courseId, $user->id);
            });
        } catch (Throwable $e) {
            // Same rule as above — log the real exception, never surface
            // $e->getMessage() to the user or store it verbatim.
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

        // Success — delete the temp images immediately per our retention policy.
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
                ->with('status', 'Paper scanned successfully — review your questions below.');
        }

        return redirect()
            ->route('scan.review', $pastQuestion)
            ->with('status', 'Paper scanned successfully — review your questions below.');
    }

    public function review(PastQuestion $pastQuestion): Response
    {
        return Inertia::render('PracticePastQuestion/PracticePastQuestions', [
            'past_question' => $pastQuestion->load(
                'course', 'semester', 'school', 'sections', 'questions', 'creator', 'updater'
            ),
        ]);
    }

    private function buildPastQuestion(array $result, ?int $courseId, int $userId): PastQuestion
    {
        $pastQuestion = PastQuestion::create([
            'course_id' => $courseId,
            'raw_course_label' => $courseId ? null : $result['course_guess'],
            'semester_id' => null,
            'school_id' => null,
            'session' => 'Unspecified',
            'title' => $result['course_guess'] ?? 'Scanned Paper_'.$userId,
            'status' => 'draft',
            'visibility' => 'private',
            'slug' => Str::slug(($result['course_guess'] ?? 'scanned-paper').'-'.Str::random(6)),
            'source_file' => 'scan',
            'created_by' => $userId,
        ]);

        // Sections are created dynamically per distinct section_label, in the
        // order they first appear — handles single-section objective papers
        // and multi-section papers (e.g. Section A objective, Section B
        // fill-in-the-blank) the same way, with no hardcoding either way.
        $sections = [];

        foreach ($result['questions'] as $index => $q) {
            $label = $q['section_label'] ?? 'Section A';

            if (! isset($sections[$label])) {
                $sections[$label] = QuestionSection::create([
                    'past_question_id' => $pastQuestion->id,
                    'title' => $label,
                    'instructions' => $q['section_instructions'] ?? null,
                    'position' => count($sections) + 1,
                ]);
            }

            $question = Question::create([
                'past_question_id' => $pastQuestion->id,
                'question_section_id' => $sections[$label]->id,
                'question_type' => $q['question_type'] ?? 'objective',
                'question_text' => $q['question_text'],
                'topic_tag' => $q['topic_tag'] ?? null,
                'marks' => 1,
                'position' => $index + 1,
                'answer_source' => ($q['answer_source'] ?? 'ai_generated') === 'from_image' ? 'human' : 'ai_generated',
                'answer_confidence' => $q['answer_confidence'] ?? null,
            ]);

            if (! empty($q['options'])) {
                foreach ($q['options'] as $optionText) {
                    $letter = strtoupper(trim(substr($optionText, 0, 1)));
                    QuestionOption::create([
                        'question_id' => $question->id,
                        'option_text' => $optionText,
                        'is_correct' => $letter === strtoupper(trim($q['answer'] ?? '')),
                    ]);
                }
            } else {
                QuestionAnswer::create([
                    'question_id' => $question->id,
                    'answer_text' => $q['answer'] ?? '',
                ]);
            }
        }

        return $pastQuestion;
    }
}
