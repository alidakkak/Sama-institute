<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CalculateCoursePriceRequest extends FormRequest
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
            'total_number_of_sessions' => 'required|numeric|min:1',
            'price' => 'required|numeric|min:1',
            'percentage_increase_over_the_default_price' => 'required|numeric|min:0,max:100',
            'rounding_threshold' => 'required|numeric|min:1',
        ];
    }
}
