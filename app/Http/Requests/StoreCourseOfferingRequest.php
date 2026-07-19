<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreCourseOfferingRequest extends FormRequest
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
                 'course_id' => [
                     'required', 'integer', 'exists:courses,id',
                     Rule::unique('course_offerings', 'course_id')
                         ->where('programme_id', Auth::user()->programme_id)
                         ->where('level_id', Auth::user()->level_id),
                 ],
            'semester_id' => ['required', 'integer', 'exists:semesters,id'],
            'type' => ['required', 'string', 'in:core,elective'],
            'is_general' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'course_id.unique' => "You've already added this course, please select another",
        ];
    }
}
