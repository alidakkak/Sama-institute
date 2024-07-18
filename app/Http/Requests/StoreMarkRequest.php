<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMarkRequest extends FormRequest
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
            'result' => 'required|array',
            'date' => 'required|date',
            'result.*' => 'required|numeric|between:0,100',
            'subject_id' => ['required', 'numeric', 'exists:subjects,id'],
            'exam_id' => ['required', 'numeric', 'exists:exams,id'],
            'student_id' => 'required|array',
            'student_id.*' => ['required', 'numeric', 'exists:students,id'],
        ];
    }
}
