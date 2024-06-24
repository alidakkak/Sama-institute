<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTeacherRequest extends FormRequest
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
            'first_name' => 'string',
            'last_name' => 'string',
            'father_name' => 'string',
            'facebook' => 'string',
            'phone' => 'string',
            'telephone' => 'nullable|string',
            'location' => 'string',
            'gender' => 'string',
            'email' => 'email',
            'date_of_birth' => 'date',
        ];
    }
}
