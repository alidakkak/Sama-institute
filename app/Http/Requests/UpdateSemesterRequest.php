<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSemesterRequest extends FormRequest
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
            //    'name' => 'required',
            'price' => 'numeric',
            'start_date' => 'date',
            'end_date' => 'date',
            'subjects' => 'array',
            'subjects.*.name' => 'string',
            'subjects.*.number_sessions_per_week' => 'numeric',
            'subjects.*.subject_id' => 'exists:subjects,id',
        ];
    }
}
