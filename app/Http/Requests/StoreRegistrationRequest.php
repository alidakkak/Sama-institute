<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRegistrationRequest extends FormRequest
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
            'student_id' => 'required|unique:students,student_id',
            'semester_id' => 'required|exists:semesters,semester_id',
            'classroom_id' => 'required|exists:classrooms,classroom_id',
            'scholarship_id' => 'required|exists:scholarships,scholarship_id',
            'subjects' => 'required|array',
            'subjects.*.subject_id' => 'required|exists:subjects,subject_id',
            'financialDues' => 'required|numeric|min:0',
        ];
    }
}
