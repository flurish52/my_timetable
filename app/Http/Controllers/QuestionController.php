<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreQuestionsRequest;
use App\Models\PastQuestion;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class QuestionController extends Controller
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
        public function store(StoreQuestionsRequest $request, PastQuestion $pastQuestion)
    {
        $data = $request->validated();

        DB::transaction(function () use ($data, $pastQuestion) {
            // Deletions first (cascades handle groups/questions/options beneath them)
            if (! empty($data['deletedSectionIds'])) {
                $pastQuestion->sections()->whereIn('id', $data['deletedSectionIds'])->delete();
            }
            if (! empty($data['deletedGroupIds'])) {
                \App\Models\QuestionGroup::whereIn('id', $data['deletedGroupIds'])->delete();
            }
            if (! empty($data['deletedQuestionIds'])) {
                Question::whereIn('id', $data['deletedQuestionIds'])->delete();
            }

            foreach ($data['sections'] as $sectionData) {
                $section = $pastQuestion->sections()->updateOrCreate(
                    ['id' => $sectionData['id'] ?? null],
                    [
                        'title' => $sectionData['title'],
                        'instructions' => $sectionData['instructions'] ?? null,
                        'position' => $sectionData['position'],
                    ]
                );

                // Ungrouped questions
                foreach ($sectionData['questions'] ?? [] as $questionData) {
                    $this->saveQuestion($pastQuestion, $section, null, $questionData);
                }

                // Grouped questions
                foreach ($sectionData['groups'] ?? [] as $groupData) {
                    $group = $section->groups()->updateOrCreate(
                        ['id' => $groupData['id'] ?? null],
                        [
                            'title' => $groupData['title'] ?? null,
                            'content' => $groupData['content'] ?? null,
                            'position' => $groupData['position'],
                        ]
                    );

                    foreach ($groupData['questions'] as $questionData) {
                        $this->saveQuestion($pastQuestion, $section, $group, $questionData);
                    }
                }
            }
        });

        return to_route('past-question.show', $pastQuestion)
            ->with('success', 'Questions saved.');

    }

    /**
     * Display the specified resource.
     */
    public function show(Question $question)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Question $question)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Question $question)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Question $question)
    {
        //
    }




    /**
     * Excel import: parses the sheet and hands off to the same save logic
     * by building the same tree shape storeQuestions expects, then redirects
     * back to the builder so the user reviews before final save.
     */
//


    /**
     * Downloadable blank template so users know the expected columns.
     */
    public function downloadImportTemplate()
    {
        return Storage::disk('public')->download('templates/past-questions-import-template.xlsx');
    }

    private function saveQuestion(PastQuestion $pastQuestion, $section, $group, array $questionData): void
    {
        $question = Question::updateOrCreate(
            ['id' => $questionData['id'] ?? null],
            [
                'past_question_id' => $pastQuestion->id,
                'question_section_id' => $section->id,
                'question_group_id' => $group?->id,
                'question_type' => $questionData['question_type'],
                'question_text' => $questionData['question_text'],
                'marks' => $questionData['marks'],
                'position' => $questionData['position'],
            ]
        );

        // Options: wipe and re-create is simplest here since option sets are small
        $question->options()->delete();
        foreach ($questionData['options'] ?? [] as $option) {
            $question->options()->create([
                'option_text' => $option['option_text'],
                'is_correct' => $option['is_correct'] ?? false,
            ]);
        }

        // Free-text answer (fill_blank / short_answer / essay model answer)
        if (! empty($questionData['answer_text'])) {
            $question->answers()->delete();
            $question->answers()->create(['answer_text' => $questionData['answer_text']]);
        }
    }
}
