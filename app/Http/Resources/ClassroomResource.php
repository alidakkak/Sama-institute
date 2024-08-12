<?php

namespace App\Http\Resources;

use App\Models\Teacher;
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
                'subjects' => $this->subjects->map(function ($subject) {
                    $subjectClassroom = $subject->pivot;
                    $teacher = Teacher::find($subjectClassroom->teacher_id);

                    return [
                        'id' => $subject->id,
                        'name' => $subject->name,
                        //     '$subjectClassroom' => $subjectClassroom,
                        'number_sessions_per_week' => $subject->number_sessions_per_week,
                        'teacherName' => $teacher ? $teacher->first_name.' '.$teacher->last_name : null,
                    ];
                }),
                'students' => $this->registrations->map(function ($registration) {
                    return [
                        'id' => $registration->student->id,
                        'fullName' => $registration->student->first_name.' '.$registration->student->last_name,
                        'created_at' => $registration->created_at->format('y-m-d'),
                        'subjectCount' => $registration->studentSubject->count(),
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
