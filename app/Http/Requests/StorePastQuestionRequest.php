<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePastQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'course_id' => ['required', 'exists:courses,id'],
            'semester_id' => ['required', 'exists:semesters,id'],
            'session' => ['required', 'string', 'regex:/^\d{4}\/\d{4}$/'], // e.g. 2024/2025
            'title' => ['required', 'string', 'max:255'],
            'instructions' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'duration_minutes' => ['nullable', 'integer', 'min:1', 'max:600'],
        ];
    }

    public function messages(): array
    {
        return [
            'session.regex' => 'Session must be in the format 2024/2025.',
        ];
    }
}

