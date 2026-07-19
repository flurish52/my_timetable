<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateTimeTableRequest extends FormRequest
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
                'required', 'integer',
                Rule::exists('course_offerings', 'course_id')
                    ->where('programme_id', Auth::user()->programme_id),
                Rule::unique('timetable', 'course_id')
                    ->where('day_of_week', $this->day_of_week)
                    ->where('start_time', $this->start_time)
                    ->ignore($this->route('timetable')),
            ],
            'day_of_week' => ['required', 'string', 'in:monday,tuesday,wednesday,thursday,friday,saturday'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'venue' => ['required', 'string', 'max:255'],
            'lecturer' => ['nullable', 'string', 'max:255'],
            'confirmed' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'course_id.exists' => 'Your department does not offer this course.',
            'course_id.unique' => "You can't add a duplicate — this course already has a slot at that exact day and time.",
        ];
    }
}
