<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentSubjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'subjects' => SubjectResource::collection($this->subject),
            'students' => $this->registrations->map(function ($registration) {
                return [
                    'id' => $registration->student->id,
                    'fullName' => $registration->student->first_name.' '.$registration->student->last_name,
                    'status' => $registration->status,
                    'subjectCount' => $registration->studentSubject->count(),
                ];
            }),
        ];
    }
}
