<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePastQuestionRequest;
use App\Http\Requests\UpdatePastQuestionRequest;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\PastQuestion;
use App\Models\ProgrammeLevelSemester;
use App\Services\PastQuestionService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PastQuestionController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (! $user) {
            return Inertia::render('PastQuestions', [
                'user' => $user,
                'past_questions' => Course::with(['past_question' => fn ($q) => $q->published()->with('semester')])->get(),
            ]);
        }

        $currentSemesterId = ProgrammeLevelSemester::where('programme_id', $user->programme_id)
            ->where('level_id', $user->level_id)
            ->value('semester_id');

        if (! $currentSemesterId) {
            return Inertia::render('PastQuestions', [
                'user' => $user,
                'past_questions' => [],
                'noSemesterSet' => true,
            ]);
        }

        $courseIds = CourseOffering::where('programme_id', $user->programme_id)
            ->where('level_id', $user->level_id)
            ->pluck('course_id');

        $pastQuestions = Course::whereIn('id', $courseIds)
            ->with(['past_question' => fn ($q) => $q->published()->with('semester')])
            ->get();

        return Inertia::render('PastQuestions', [
            'user' => $user,
            'past_questions' => $pastQuestions,
        ]);
    }

    public function contributorIndex()
    {
        return Inertia::render('PastQuestions/Index', [
            'pastQuestions' => PastQuestion::query()
                ->pastPapers()
                ->where('school_id', auth()->user()->school_id)
                ->where('created_by', auth()->id())
                ->with('course:id,code,title')
                ->withCount(['questions'])
                ->latest()
                ->get()
                ->map(fn ($pq) => [
                    'id' => $pq->id,
                    'title' => $pq->title,
                    'course' => $pq->course?->code,
                    'session' => $pq->session,
                    'questions_count' => $pq->questions_count,
                    'updated_at' => $pq->updated_at->diffForHumans(),
                    'status' => $pq->status,
                ]),
        ]);
    }

    public function showCoursePapers($slug, PastQuestionService $pdfService)
    {
        $course = Course::with(['past_question' => fn ($q) => $q->published()->with(['semester', 'creator'])])
            ->where('code', $slug)
            ->firstOrFail();

        foreach ($course->past_question as $paper) {
            $paper->source_file = $pdfService->resolvePdf($paper);
        }

        return Inertia::render('PastQuestionsPerCourse', [
            'past_question' => $course,
        ]);
    }

    public function startPractice($slug, $question_slug)
    {
        $pastQuestion = PastQuestion::with('course', 'semester', 'school', 'sections', 'questions', 'creator', 'updater')
            ->findOrFail($question_slug);

        $this->authorizeView($pastQuestion);

        return Inertia::render('PracticePastQuestion/PracticePastQuestions', [
            'past_question' => $pastQuestion,
        ]);
    }

    public function practice($past_question)
    {
        $pastQuestion = PastQuestion::with(
            'course', 'semester', 'school', 'sections',
            'questions', 'questions.options', 'questions.answers', 'questions.media',
            'creator', 'updater'
        )->findOrFail($past_question);

        $this->authorizeView($pastQuestion);

        return Inertia::render('PracticePastQuestion/StartPractice', [
            'past_question' => $pastQuestion,
        ]);
    }

    public function create()
    {
        return Inertia::render('PastQuestions/Create', [
            'courses' => Course::query()
                ->whereHas('course_offering', function ($query) {
                    $query->where('programme_id', auth()->user()->programme_id);
                })
                ->orderBy('title')
                ->get(['id', 'code', 'title']),

            'semesters' => \App\Models\Semester::orderBy('id')->get(['id', 'name']),
        ]);
    }

    public function store(StorePastQuestionRequest $request)
    {
        $data = $request->validated();
        $pastQuestion = PastQuestion::create([
            ...$data,
            'school_id' => auth()->user()->school_id,
            'slug' => $this->uniqueSlug($data['title'], $data['session']),
            'created_by' => auth()->id(),
        ]);

        return to_route('past-questions.build', $pastQuestion);
    }

    public function build(PastQuestion $pastQuestion)
    {
        $pastQuestion->load([
            'course:id,code,title',
            'sections' => fn ($q) => $q->orderBy('position'),
            'sections.groups' => fn ($q) => $q->orderBy('position'),
            'sections.groups.questions' => fn ($q) => $q->orderBy('position'),
            'sections.groups.questions.options',
            'sections.groups.questions.answers',
            'sections.questions' => fn ($q) => $q->whereNull('question_group_id')->orderBy('position'),
            'sections.questions.options',
            'sections.questions.answers',
        ]);

        return Inertia::render('PastQuestions/Build', [
            'pastQuestion' => $pastQuestion,
        ]);
    }

    public function show(PastQuestion $pastQuestion)
    {
        $pastQuestion->load([
            'course:id,code,title',
            'semester:id,name',
            'school:id,name',
            'sections' => fn ($q) => $q->orderBy('position'),
            'sections.groups' => fn ($q) => $q->orderBy('position'),
            'sections.groups.questions' => fn ($q) => $q->orderBy('position'),
            'sections.groups.questions.options',
            'sections.groups.questions.answers',
            'sections.questions' => fn ($q) => $q->whereNull('question_group_id')->orderBy('position'),
            'sections.questions.options',
            'sections.questions.answers',
        ]);

        return Inertia::render('PastQuestions/Show', [
            'pastQuestion' => $pastQuestion,
            'justCreated' => (bool) session('success'),
        ]);
    }

    public function edit(PastQuestion $pastQuestion)
    {
        //
    }

    public function update(UpdatePastQuestionRequest $request, PastQuestion $pastQuestion)
    {
        //
    }

    public function destroy(PastQuestion $pastQuestion)
    {
        //
    }

    public function togglePublish(PastQuestion $pastQuestion)
    {
        abort_unless((int) $pastQuestion->created_by === (int) Auth::id(), 403);
        abort_if($pastQuestion->kind === 'notes_quiz', 403);

        $togglingToPublished = $pastQuestion->status !== 'published';

        if ($togglingToPublished) {
            $hasQuestions = $pastQuestion->sections()
                ->where(function ($query) {
                    $query->whereHas('questions')
                        ->orWhereHas('groups.questions');
                })
                ->exists();

            if (! $hasQuestions) {
                return back()->with('error', 'Add at least one question before publishing this paper.');
            }
        }

        $pastQuestion->status = $togglingToPublished ? 'published' : 'draft';
        $pastQuestion->save();

        return back()->with(
            'success',
            $pastQuestion->status === 'published' ? 'Past question published.' : 'Moved back to draft.'
        );
    }

    private function uniqueSlug(string $title, string $session): string
    {
        $base = Str::slug($title . '-' . str_replace('/', '-', $session));
        $slug = $base;
        $i = 1;

        while (PastQuestion::where('slug', $slug)->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }

    /** Public if published; otherwise only the owner or an admin. 404 (not 403) so IDs can't be probed. */
    private function authorizeView(PastQuestion $pq): void
    {
        $user = auth()->user();

        $allowed = ($pq->visibility === 'published' && $pq->status === 'published')
            || ($user && ((int) $pq->created_by === (int) $user->id || $user->hasRole('admin')));

        abort_unless($allowed, 404);
    }
}
