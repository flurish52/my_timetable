<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\PastQuestion;
use App\Http\Requests\StorePastQuestionRequest;
use App\Http\Requests\UpdatePastQuestionRequest;
use App\Services\PastQuestionService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PastQuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
            $user = Auth::user();
            if ($user) {
                return Inertia::render('PastQuestions', [
                    'user' => $user,
                    'past_questions' => Course::with(['past_question' =>
                        function ($query) {
                        $query->where('status', 'published')->with('semester');
                    }])->get()
                ]);
            } else {
                return Inertia::render('PastQuestions', [
                    'user' => $user,
                    'past_questions' => Course::with('past_question')
                        ->get(),
                ]);
            }
    }

    public function contributorIndex()
    {
        return Inertia::render('PastQuestions/Index', [
            'pastQuestions' => PastQuestion::query()
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

    public function showCoursePapers($slug, PastQuestionService $pdfService) {
        $course = Course::with(['past_question' => function ($query) {
            $query->where('status', 'published')->with('semester');
        }])
            ->where('code', $slug)
            ->first();

        foreach ($course->past_question as $paper) {
            $paper->source_file = $pdfService->resolvePdf($paper);
        }

        return inertia('PastQuestionsPerCourse', [
            'past_question' => $course,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function startPractice($slug, $question_slug)
    {
        return inertia::render('PracticePastQuestion/PracticePastQuestions', [
                'past_question' => PastQuestion::with('course', 'semester',
                    'school', 'sections', 'questions',
                    'creator', 'updater')
                    ->where('id', $question_slug)->first(),
        ]);
    }

    public function practice($past_question)
    {
        return inertia::render('PracticePastQuestion/StartPractice', [
            'past_question' => PastQuestion::with('course', 'semester',
                'school', 'sections', 'questions', 'questions.options', 'questions.answers',
                'questions.media', 'creator', 'updater')
                ->where('id', $past_question)->first(),
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

    /**
     * Store a newly created resource in storage.
     */
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

    /**
     * Display the specified resource.
     */

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

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PastQuestion $pastQuestion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePastQuestionRequest $request, PastQuestion $pastQuestion)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PastQuestion $pastQuestion)
    {
        //
    }


    public function togglePublish(PastQuestion $pastQuestion)
    {
        abort_unless((int) $pastQuestion->created_by === (int) Auth::id(), 403);

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
}
