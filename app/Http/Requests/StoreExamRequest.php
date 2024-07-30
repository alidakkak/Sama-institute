<?php

namespace App\Http\Requests;

use App\Models\Exam;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreExamRequest extends FormRequest
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
            'percent' => 'required|integer|min:0|max:100',
            'semester_id' => 'required|integer|exists:semesters,id',
        ];
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            $semesterId = $this->input('semester_id');
            $percent = $this->input('percent');

            $totalPercent = Exam::where('semester_id', $semesterId)
                ->sum('percent') + $percent;

            if ($totalPercent > 100) {
                $validator->errors()->add('percent', 'The total percentage for the semester exceeds 100%.');
            }
        });
    }
}
