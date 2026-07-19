<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreQuestionsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'sections' => ['required', 'array', 'min:1'],
            'sections.*.id' => ['nullable', 'integer', 'exists:question_sections,id'],
            'sections.*.title' => ['required', 'string', 'max:255'],
            'sections.*.instructions' => ['nullable', 'string'],
            'sections.*.position' => ['required', 'integer', 'min:1'],

            // Ungrouped questions sitting directly under a section
            'sections.*.questions' => ['nullable', 'array'],
            'sections.*.questions.*.id' => ['nullable', 'integer', 'exists:questions,id'],
            'sections.*.questions.*.question_type' => ['required', 'in:objective,true_false,fill_blank,short_answer,essay'],
            'sections.*.questions.*.question_text' => ['required', 'string'],
            'sections.*.questions.*.marks' => ['required', 'integer', 'min:1'],
            'sections.*.questions.*.position' => ['required', 'integer', 'min:1'],
            'sections.*.questions.*.options' => ['nullable', 'array'],
            'sections.*.questions.*.options.*.option_text' => ['required', 'string'],
            'sections.*.questions.*.options.*.is_correct' => ['boolean'],
            'sections.*.questions.*.answer_text' => ['nullable', 'string'],

            // Grouped questions (shared passage/content)
            'sections.*.groups' => ['nullable', 'array'],
            'sections.*.groups.*.id' => ['nullable', 'integer', 'exists:question_groups,id'],
            'sections.*.groups.*.title' => ['nullable', 'string', 'max:255'],
            'sections.*.groups.*.content' => ['nullable', 'string'],
            'sections.*.groups.*.position' => ['required', 'integer', 'min:1'],

            'sections.*.groups.*.questions' => ['required', 'array', 'min:1'],
            'sections.*.groups.*.questions.*.id' => ['nullable', 'integer', 'exists:questions,id'],
            'sections.*.groups.*.questions.*.question_type' => [
                'required', 'in:objective,true_false,fill_blank,short_answer,essay',
            ],
            'sections.*.groups.*.questions.*.question_text' => ['required', 'string'],
            'sections.*.groups.*.questions.*.marks' => ['required', 'integer', 'min:1'],
            'sections.*.groups.*.questions.*.position' => ['required', 'integer', 'min:1'],

            'sections.*.groups.*.questions.*.options' => ['nullable', 'array'],
            'sections.*.groups.*.questions.*.options.*.id' => ['nullable', 'integer', 'exists:question_options,id'],
            'sections.*.groups.*.questions.*.options.*.option_text' => ['required_with:sections.*.groups.*.questions.*.options', 'string'],
            'sections.*.groups.*.questions.*.options.*.is_correct' => ['boolean'],

            'sections.*.groups.*.questions.*.answer_text' => ['nullable', 'string'], // for fill_blank/short_answer/essay

            // IDs removed on the frontend, passed so we know what to delete
            'deletedSectionIds' => ['array'],
            'deletedGroupIds' => ['array'],
            'deletedQuestionIds' => ['array'],
        ];
    }

    public function withValidator($validator)
    {
        // Objective/true_false questions must have at least two options, one marked correct
        $validator->after(function ($validator) {
            $check = function ($question, string $path) use ($validator) {
                $needsOptions = in_array($question['question_type'], ['objective', 'true_false']);
                $options = $question['options'] ?? [];

                if (! $needsOptions) {
                    return;
                }

                if (count($options) < 2) {
                    $validator->errors()->add("$path.options", 'This question type needs at least two options.');
                }

                if (collect($options)->where('is_correct', true)->isEmpty()) {
                    $validator->errors()->add("$path.options", 'Mark at least one option as correct.');
                }
            };

            foreach ($this->input('sections', []) as $si => $section) {
                foreach ($section['questions'] ?? [] as $qi => $question) {
                    $check($question, "sections.$si.questions.$qi");
                }

                foreach ($section['groups'] ?? [] as $gi => $group) {
                    foreach ($group['questions'] ?? [] as $qi => $question) {
                        $check($question, "sections.$si.groups.$gi.questions.$qi");
                    }
                }
            }
        });
    }
}
