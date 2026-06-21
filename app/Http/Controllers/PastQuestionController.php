<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\PastQuestion;
use App\Http\Requests\StorePastQuestionRequest;
use App\Http\Requests\UpdatePastQuestionRequest;
use App\Services\PastQuestionService;
use Illuminate\Support\Facades\Auth;
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
                    'past_questions' => Course::with('past_question')
                    ->get(),
                ]);
            } else {
                return Inertia::render('PastQuestions', [
                    'user' => $user,
                    'past_questions' => Course::with('past_question')
                        ->get(),
                ]);
            }
    }
    public function showCoursePapers($slug, PastQuestionService $pdfService) {
        $course = Course::with('past_question.semester')
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
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePastQuestionRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(PastQuestion $pastQuestion)
    {
        //
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
}
