<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTeacherRequest extends FormRequest
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
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'father_name' => 'required|string',
            'facebook' => 'nullable|string',
            'phone' => 'required|string',
            'telephone' => 'nullable|string',
            'location' => 'required|string',
            'gender' => 'required|string',
            'email' => 'required|email',
            'date_of_birth' => 'required|date',
        ];
    }
}
