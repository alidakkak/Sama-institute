<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStudentRequest extends FormRequest
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
            'name' => 'required|string',
            'user_name' => 'required|string|unique:students',
            'birthdate' => 'required',
            'password' => 'min:8|required_with:password_confirmation|same:password_confirmation',
            'academic_year_id' => ['required', Rule::exists('academic_years', 'id')],
            'grade_id' => ['required', Rule::exists('grades', 'id')],
            'classroom_ids' => 'array|required',
            'classroom_ids.*' => ['required', Rule::exists('classrooms', 'id')],
            'subject_ids' => 'array|required',
            'subject_ids.*' => ['required', Rule::exists('subjects', 'id')],
        ];
    }
}
