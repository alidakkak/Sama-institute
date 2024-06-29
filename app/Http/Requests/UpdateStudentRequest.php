<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'first_name' => 'string|max:255',
            'last_name' => 'string|max:255',
            'age' => 'integer|min:0',
            'date_of_birth' => 'date',
            'place_of_birth' => 'string|max:255',
            'gender' => 'string',
            'marital_status' => 'nullable|string|max:255',
            'previous_educational_status' => 'string|max:255',
            //'phone_number' => 'string|unique:students,phone_number|max:15',
            'phone_number' => 'string|max:15',
            'telephone_number' => 'nullable|string|max:15',
            'facebook' => 'nullable|string|max:255',
            'instagram' => 'nullable|string|max:255',
            'location' => 'string|max:255',
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
            'image' => 'image|mimes:jpeg,png,jpg,svg|max:2048',
        ];
    }
}
