<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            // 'age' => 'required|integer|min:0',
            'date_of_birth' => 'nullable|date',
            'place_of_birth' => 'nullable|string|max:255',
            //  'gender' => 'required|string',
            'marital_status' => 'nullable|string|max:255',
            'previous_educational_status' => 'nullable|string|max:255',
            'phone_number' => 'required|string|unique:students,phone_number|max:15',
            'student_phone_number' => 'nullable|string|max:15',
            'telephone_number' => 'nullable|string|max:15',
            'facebook' => 'nullable|string|max:255',
            //  'instagram' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'father_name' => 'nullable|string|max:255',
            'father_work' => 'nullable|string|max:255',
            'father_of_birth' => 'nullable|date',
            'father_Healthy' => 'nullable|string',
            'mother_name' => 'nullable|string|max:255',
            'mother_work' => 'nullable|string|max:255',
            'mother_of_birth' => 'nullable|date',
            'mother_Healthy' => 'nullable|string',
            'other_name' => 'nullable|string|max:255',
            'other_work' => 'nullable|string|max:255',
            'other_of_birth' => 'nullable|date',
            'other_Healthy' => 'nullable|string',
            'note' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
        ];
    }
}
