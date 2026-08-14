<?php

namespace App\Http\Controllers;

use App\Http\Requests\PastQuestionImportRequest;
use App\Imports\PastQuestionsImport;
use App\Models\PastQuestion;
use App\Models\Question;
use App\Services\QuestionTextParser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpWord\IOFactory as WordIOFactory;
use Smalot\PdfParser\Parser as PdfParser;

class PastQuestionImportController extends Controller
{
    /**
     * Landing page — every import method in one place.
     */
    public function index(PastQuestion $pastQuestion)
    {
        return Inertia::render('PastQuestions/Import', [
         'pastQuestion' => $pastQuestion->only('id', 'title', 'course_id'),
        ]);
    }

    /**
     * Paste/type-in format (ImportQuestionsStep.vue). Parsing already
     * happened client-side; this just persists it.
     */
    public function importPasted(PastQuestionImportRequest $request, PastQuestion $pastQuestion)
    {
        $count = DB::transaction(
            fn () => $this->createQuestionsFromParsed($request->validated('questions'), $pastQuestion)
        );

        return back()->with('success', "Imported {$count} question(s).");
    }

    /**
     * Excel/CSV upload — the sectioned template (PastQuestionsImport).
     * This was missing entirely, which is why Excel uploads had nowhere
     * correct to go and were falling through to importPasted() instead.
     */
    public function importQuestions(Request $request, PastQuestion $pastQuestion)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'],
        ]);

        $import = new PastQuestionsImport();
        Excel::import($import, $request->file('file'));

        $count = DB::transaction(function () use ($import, $pastQuestion) {
            $created = 0;

            foreach ($import->sections as $sectionData) {
                $section = $pastQuestion->sections()->create([
                    'title' => $sectionData['title'],
                    'position' => $sectionData['position'],
                ]);

                foreach ($sectionData['groups'] as $groupKey => $groupData) {
                    $group = $groupKey !== '__none__'
                        ? $section->groups()->create([
                            'title' => $groupData['title'],
                            'content' => $groupData['content'],
                            'position' => $groupData['position'],
                        ])
                        : null;

                    foreach ($groupData['questions'] as $questionData) {
                        $question = Question::create([
                            'past_question_id' => $pastQuestion->id,
                            'question_section_id' => $section->id,
                            'question_group_id' => $group?->id,
                            'question_type' => $questionData['question_type'],
                            'question_text' => $questionData['question_text'],
                            'marks' => $questionData['marks'],
                            'position' => $questionData['position'],
                        ]);

                        foreach ($questionData['options'] as $option) {
                            $question->options()->create($option);
                        }
                        if ($questionData['answer_text']) {
                            $question->answers()->create(['answer_text' => $questionData['answer_text']]);
                        }
                        $created++;
                    }
                }
            }

            return $created;
        });

        return to_route('past-questions.build', $pastQuestion)
            ->with('success', "Imported {$count} question(s). Review them below.");
    }

    /**
     * Word (.docx) or PDF upload — extracted then run through the same
     * Q:/Type:/Answer:/Tip: parser as the paste flow, in strict mode.
     */
    public function importDocument(Request $request, PastQuestion $pastQuestion)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:20480'],
        ]);

        $file = $request->file('file');
        $extension = strtolower($file->getClientOriginalExtension());

        $text = match ($extension) {
            'pdf' => $this->extractPdfText($file->getRealPath()),
            'doc', 'docx' => $this->extractWordText($file->getRealPath()),
            default => '',
        };

        $parsed = (new QuestionTextParser())->parseValid($text, strict: true);

        if (empty($parsed)) {
            return back()->withErrors([
                'file' => 'No valid questions found. Each question needs a "Q:" line.',
            ]);
        }

        $count = DB::transaction(fn () => $this->createQuestionsFromParsed($parsed, $pastQuestion));

        return to_route('past-questions.build', $pastQuestion)
            ->with('success', "Imported {$count} question(s) from {$file->getClientOriginalName()}. Review them below.");
    }

    public function downloadImportTemplate()
    {
        return Storage::disk('public')->download('templates/past-questions-import-template.xlsx');
    }

    private function extractPdfText(string $path): string
    {
        return (new PdfParser())->parseFile($path)->getText();
    }

    private function extractWordText(string $path): string
    {
        $document = WordIOFactory::load($path);
        $text = '';

        foreach ($document->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                $text .= $this->extractElementText($element) . "\n";
            }
        }

        return $text;
    }

    private function extractElementText($element): string
    {
        if ($element instanceof \PhpOffice\PhpWord\Element\Text) {
            return (string) $element->getText();
        }

        // Containers (TextRun, Section, table cells, etc.) — recurse into children.
        if (method_exists($element, 'getElements')) {
            return collect($element->getElements())
                ->map(fn ($child) => $this->extractElementText($child))
                ->implode(' ');
        }

        // Titles/other leaf elements that expose getText() directly as a string.
        if (method_exists($element, 'getText')) {
            $text = $element->getText();
            return is_string($text) ? $text : '';
        }

        return '';
    }

    /**
     * Shared by paste-import, document-import: turns parsed question
     * arrays into Question/QuestionOption/QuestionAnswer rows.
     */
    private function createQuestionsFromParsed(array $parsedQuestions, PastQuestion $pastQuestion): int
    {
        $typeMap = ['mcq' => 'objective', 'tf' => 'true_false', 'short' => 'short_answer', 'essay' => 'essay'];

        $section = $pastQuestion->sections()->firstOrCreate(
            ['title' => 'Imported questions'],
            ['position' => ($pastQuestion->sections()->max('position') ?? 0) + 1]
        );

        $position = $section->questions()->max('position') ?? 0;
        $created = 0;

        foreach ($parsedQuestions as $q) {
            $position++;
            $type = $typeMap[$q['type']] ?? 'short_answer';

            $question = $section->questions()->create([
                'past_question_id' => $pastQuestion->id,
                'question_group_id' => null,
                'question_type' => $type,
                'question_text' => $q['question'],
                'marks' => 1,
                'position' => $position,
            ]);

            if ($type === 'objective' && ! empty($q['answer'])) {
                $correctIndex = ord(strtoupper(trim($q['answer']))[0]) - ord('A');
                foreach ($q['options'] as $i => $optionText) {
                    $question->options()->create([
                        'option_text' => $optionText,
                        'is_correct' => $i === $correctIndex,
                    ]);
                }
            } elseif ($type === 'true_false') {
                $isTrue = strtolower(trim($q['answer'] ?? '')) === 'true';
                $question->options()->createMany([
                    ['option_text' => 'True', 'is_correct' => $isTrue],
                    ['option_text' => 'False', 'is_correct' => ! $isTrue],
                ]);
            }

            if (! empty($q['tip'])) {
                $question->answers()->create(['answer_text' => $q['tip']]);
            }

            $created++;
        }

        return $created;
    }
}
