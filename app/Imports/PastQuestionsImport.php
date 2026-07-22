<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

/**
 * Expected columns (see the downloadable template):
 *
 * section | group | group_content | question_type | question_text | marks
 * | option_a | option_b | option_c | option_d | option_e | correct_option | answer_text
 *
 * One row = one question. Rows are grouped by "section" (in first-seen order),
 * then by "group" within that section (blank group = ungrouped question).
 * "correct_option" is a letter (a-e) matching which option column is correct,
 * used only for objective/true_false types.
 */
class PastQuestionsImport implements ToCollection, WithHeadingRow
{
    public array $sections = [];

    public function collection(Collection $rows)
    {
        $sectionPosition = 0;

        foreach ($rows as $row) {
            $sectionTitle = trim((string) $row['section']);
            if ($sectionTitle === '') {
                continue; // skip blank rows
            }

            if (! isset($this->sections[$sectionTitle])) {
                $sectionPosition++;
                $this->sections[$sectionTitle] = [
                    'title' => $sectionTitle,
                    'position' => $sectionPosition,
                    'groups' => [],
                ];
            }

            $groupTitle = trim((string) ($row['group'] ?? ''));
            $groupKey = $groupTitle !== '' ? $groupTitle : '__none__';

            if (! isset($this->sections[$sectionTitle]['groups'][$groupKey])) {
                $this->sections[$sectionTitle]['groups'][$groupKey] = [
                    'title' => $groupTitle !== '' ? $groupTitle : null,
                    'content' => $groupTitle !== '' ? trim((string) ($row['group_content'] ?? '')) : null,
                    'position' => count($this->sections[$sectionTitle]['groups']) + 1,
                    'questions' => [],
                ];
            }

            $questionType = strtolower(trim((string) $row['question_type']));
            $options = [];

            if (in_array($questionType, ['objective', 'true_false'])) {
                $correctLetter = strtolower(trim((string) ($row['correct_option'] ?? '')));

                foreach (['a', 'b', 'c', 'd', 'e'] as $letter) {
                    $text = trim((string) ($row["option_{$letter}"] ?? ''));
                    if ($text === '') {
                        continue;
                    }
                    $options[] = [
                        'option_text' => $text,
                        'is_correct' => $letter === $correctLetter,
                    ];
                }
            }

            $this->sections[$sectionTitle]['groups'][$groupKey]['questions'][] = [
                'question_type' => $questionType,
                'question_text' => trim((string) $row['question_text']),
                'marks' => (int) ($row['marks'] ?? 1),
                'position' => count($this->sections[$sectionTitle]['groups'][$groupKey]['questions']) + 1,
                'options' => $options,
                'answer_text' => trim((string) ($row['answer_text'] ?? '')) ?: null,
            ];
        }
    }
}
