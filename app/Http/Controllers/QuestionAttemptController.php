<?php

namespace App\Http\Controllers;

use App\Models\QuestionAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuestionAttemptController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'past_question_id'          => ['required', 'integer', 'exists:past_questions,id'],
            'score'                     => ['required', 'integer', 'min:0'],
            'total_attempted'           => ['required', 'integer', 'min:0'],
            'correct_answers'           => ['required', 'integer', 'min:0'],
            'time_taken'                => ['required', 'integer', 'min:0'],
            'answers'                   => ['nullable', 'array'],
            'answers.*.question_id'     => ['required', 'integer', 'exists:questions,id'],
            'answers.*.question_option_id' => ['nullable', 'integer', 'exists:question_options,id'],
            'answers.*.answer_text'     => ['nullable', 'string'],
            'answers.*.is_correct'      => ['nullable', 'boolean'],
        ]);

        try {
            DB::transaction(function () use ($validated) {
                $attempt = QuestionAttempt::create([
                    'user_id'          => auth()->id(),
                    'past_question_id' => $validated['past_question_id'],
                    'score'            => $validated['score'],
                    'total_attempted'  => $validated['total_attempted'],
                    'correct_answers'  => $validated['correct_answers'],
                    'started_at'       => now()->subSeconds($validated['time_taken']),
                    'submitted_at'     => now(),
                ]);

//                if (!empty($validated['answers'])) {
//                    $rows = array_map(fn($a) => [
//                        'question_attempt_id' => $attempt->id,
//                        'question_id'         => $a['question_id'],
//                        'question_option_id'  => $a['question_option_id'] ?? null,
//                        'answer_text'         => $a['answer_text'] ?? null,
//                        'is_correct'          => $a['is_correct'] ?? null,
//                        'marks_awarded'       => 0,
//                        'created_at'          => now(),
//                        'updated_at'          => now(),
//                    ], $validated['answers']);

//                    QuestionAttemptAnswer::insert($rows);
//                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Attempt submitted successfully.',
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save attempt. Please try again.',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(QuestionAttempt $questionAttempt)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(QuestionAttempt $questionAttempt)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, QuestionAttempt $questionAttempt)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(QuestionAttempt $questionAttempt)
    {
        //
    }
}
