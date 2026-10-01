<?php

namespace App\Actions;

use App\Models\PastQuestion;
use App\Models\Question;
use App\Models\QuestionAnswer;
use App\Models\QuestionOption;
use App\Models\QuestionSection;
use Illuminate\Support\Str;

class BuildScannedPastQuestion
{
    public function handle(array $result, int $userId, ?int $courseId, string $kind = 'past_question'): PastQuestion
    {
        $isNotes = $kind === 'notes_quiz';
        $label = $isNotes ? ($result['topic'] ?? null) : ($result['course_guess'] ?? null);
        $title = Str::limit($label ?: ($isNotes ? 'Notes Quiz' : 'Scanned Paper_' . $userId), 200, '');

        $pastQuestion = PastQuestion::create([
            'course_id' => $courseId,
            'raw_course_label' => $courseId ? null : ($result['course_guess'] ?? null),
            'semester_id' => null,
            'school_id' => null,
            'session' => 'Unspecified',
            'title' => $title,
            'status' => 'draft',
            'visibility' => 'private',
            'kind' => $kind,
            'slug' => Str::slug($title . '-' . Str::random(6)),
            'source_file' => $isNotes ? 'scan_notes' : 'scan',
            'created_by' => $userId,
        ]);

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
                'explanation' => $q['explanation'] ?? null,
                'source_excerpt' => $q['source_excerpt'] ?? null,
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
