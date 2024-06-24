<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStudentRequest extends FormRequest
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
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'age' => 'required|integer|min:0',
            'date_of_birth' => 'required|date',
            'place_of_birth' => 'required|string|max:255',
            'gender' => 'required|string|in:male,female,other',
            'marital_status' => 'nullable|string|max:255',
            'previous_educational_status' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:15',
            'telephone_number' => 'nullable|string|max:15',
            'facebook' => 'nullable|string|max:255',
            'instagram' => 'nullable|string|max:255',
            'location' => 'required|string|max:255',
            'father_name' => 'nullable|string|max:255',
            'father_work' => 'nullable|string|max:255',
            'father_of_birth' => 'nullable|date',
            'mother_name' => 'nullable|string|max:255',
            'mother_work' => 'nullable|string|max:255',
            'mother_of_birth' => 'nullable|date',
            'other_name' => 'nullable|string|max:255',
            'other_work' => 'nullable|string|max:255',
            'other_of_birth' => 'nullable|date',
            'note1' => 'nullable|string',
            'note2' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,svg|max:2048',
            'user_name' => 'required|string|unique:students,user_name|max:255',
            'semester_id' => ['required', Rule::exists('semesters', 'id')],
            'scholarship_id' => ['nullable', Rule::exists('scholarships', 'id')],
            'classroom_ids' => 'array|required',
            'classroom_ids.*' => ['required', Rule::exists('classrooms', 'id')],
            'subject_ids' => 'array|required',
            'subject_ids.*' => ['required', Rule::exists('subjects', 'id')],
        ];
    }
}
