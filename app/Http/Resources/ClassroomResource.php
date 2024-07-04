<?php

namespace App\Http\Resources;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClassroomResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if ($request->route()->uri() === 'api/classrooms/{classroomId}') {
            return [
                'id' => $this->id,
                'name' => $this->name,
                'subjects' => SubjectResource::collection($this->subjects),
                'students' => $this->registrations->map(function ($registration) {
                    return [
                        'id' => $registration->student->id,
                        'fullName' => $registration->student->first_name . ' ' . $registration->student->last_name,
                    ];
                }),
            ];
        }
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }
}
