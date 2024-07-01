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
        ];
    }
}
