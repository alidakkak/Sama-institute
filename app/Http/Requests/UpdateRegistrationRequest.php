<?php

namespace App\Http\Requests;

use App\Models\Registration;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateRegistrationRequest extends FormRequest
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
            'student_id' => 'exists:students,id',
            'semester_id' => 'exists:semesters,id',
            'classroom_id' => 'exists:classrooms,id',
            'scholarship_id' => 'nullable|exists:scholarships,id',
            'subjects' => 'array',
            'subjects.*.subject_id' => 'exists:subjects,id',
            'financialDues' => 'numeric|min:0',
            'after_discount' => 'nullable|numeric|min:0',
            'total_number_of_sessions' => 'numeric|min:0',
        ];
    }
}
