<?php

namespace App\Http\Requests;

use App\Models\Registration;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreRegistrationRequest extends FormRequest
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
        if (request()->route()->uri() === 'api/withdrawalFromTheCourse') {
            return [
                'registration_id' => 'required|exists:registrations,id',
                'value' => 'required|numeric',
                'number' => 'required|numeric',
            ];
        }

        if (request()->route()->uri() === 'api/quickRegistration') {
            return [
                'semester_id' => 'required|exists:semesters,id',
                'classroom_id' => 'required|exists:classrooms,id',
                'scholarship_id' => 'nullable|exists:scholarships,id',
                'subjects' => 'required|array',
                'subjects.*.subject_id' => 'required|exists:subjects,id',
                'financialDues' => 'required|numeric|min:0',
                'amountOfDelay' => 'required|numeric|min:0',
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'father_name' => 'required|string',
                'phone_number' => 'required|string',
            ];
        }

        return [
            'student_id' => 'required|exists:students,id',
            'semester_id' => 'required|exists:semesters,id',
            'classroom_id' => 'required|exists:classrooms,id',
            'scholarship_id' => 'nullable|exists:scholarships,id',
            'subjects' => 'required|array',
            'subjects.*.subject_id' => 'required|exists:subjects,id',
            'financialDues' => 'required|numeric|min:0',
            'amountOfDelay' => 'required|numeric|min:0',
        ];
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            $studentId = $this->input('student_id');
            $semesterId = $this->input('semester_id');

            $existingRegistration = Registration::where('student_id', $studentId)
                ->where('semester_id', $semesterId)
                ->first();

            if ($existingRegistration) {
                $validator->errors()->add('student_id', 'The student is already registered for this course.');
            }
        });
    }
}
