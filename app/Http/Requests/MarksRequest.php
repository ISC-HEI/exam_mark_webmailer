<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MarksRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'course_name' => 'bail|required|string|min:3',
            'exam_name' => 'bail|required|string|min:3',
            'message' => 'bail|required|string|min:3',
            'teacher_email' => 'bail|required|email',
            'global_attachment' => 'nullable|array|max:5',
            'global_attachment.*' => 'file|max:10240',
            'students' => 'bail|required|array|min:1',
            'students.*.name' => 'bail|required|string|min:2',
            'students.*.email' => 'bail|required|email',
            'students.*.mark' => 'bail|required|numeric|min:1|max:6',
            'students.*.individual_file' => 'nullable|file|max:5120',
            'students.*.temp_file_path' => 'nullable|string',
            'students.*.temp_file_name' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'students.*.name.required' => 'Each student must have a name.',
            'students.*.email.required' => 'Each student must have an email.',
            'students.*.email.email' => 'Each student email must be valid.',
            'students.*.mark.required' => 'Each student must have a mark.',
            'students.*.mark.numeric' => 'Each student mark must be a number.',
            'students.*.mark.min' => 'Each student mark must be at least 1.',
            'students.*.mark.max' => 'Each student mark cannot be more than 6.',
        ];
    }
}
