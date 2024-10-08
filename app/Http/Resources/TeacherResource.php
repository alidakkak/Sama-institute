<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeacherResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'father_name' => $this->father_name,
            'facebook' => $this->facebook,
            'phone' => $this->phone,
            'telephone' => $this->telephone,
            'email' => $this->email,
            'location' => $this->location,
            'gender' => $this->gender,
            'date_of_birth' => $this->date_of_birth,
            'status' => $this->status,
            'teacherPayment' => TeacherSalaryResource::collection($this->teacherSalary),
            'created_at' => $this->created_at->format('y-m-d'),
            'subject' => $this->subjects->map(function ($subject) {
                return [
                    'id' => $subject->id,
                    'name' => $subject->name,
                    'number_sessions_per_week' => $subject->number_sessions_per_week,
                    'classroom_id' => $subject->pivot->classroom_id,
                ];
            }),
        ];
    }
}
