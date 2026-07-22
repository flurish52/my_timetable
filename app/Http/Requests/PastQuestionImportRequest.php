<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PastQuestionImportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Adjust to your contributor authorization (e.g. policy on Paper)
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
            'questions' => ['required', 'array', 'min:1'],
            'questions.*.question' => ['required', 'string'],
            'questions.*.type' => ['required', 'in:mcq,short,essay,tf'],
            'questions.*.options' => ['nullable', 'array'],
            'questions.*.options.*' => ['string'],
            'questions.*.answer' => ['nullable', 'string'],
            'questions.*.tip' => ['nullable', 'string'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            foreach ($this->input('questions', []) as $i => $q) {
                if ($q['type'] === 'mcq') {
                    if (count($q['options'] ?? []) < 2) {
                        $validator->errors()->add("questions.$i.options", 'MCQ needs at least 2 options.');
                    }
                    if (empty($q['answer'])) {
                        $validator->errors()->add("questions.$i.answer", 'MCQ needs an answer letter (A, B, C...).');
                    } elseif (! $this->answerLetterIsInRange($q['answer'], $q['options'] ?? [])) {
                        $validator->errors()->add("questions.$i.answer", 'Answer letter does not match any option.');
                    }
                }

                if ($q['type'] === 'tf' && ! empty($q['answer']) && ! preg_match('/^(true|false)$/i', trim($q['answer']))) {
                    $validator->errors()->add("questions.$i.answer", 'True/False answer must be "True" or "False".');
                }
            }
        });
    }

    private function answerLetterIsInRange(string $answer, array $options): bool
    {
        $index = ord(strtoupper(trim($answer))[0] ?? '') - ord('A');

        return $index >= 0 && $index < count($options);
    }

}
