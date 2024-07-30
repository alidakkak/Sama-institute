<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SemesterResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if (is_null($this->actual_start_date)) {
            $status = 'انتظار';
        } elseif (! is_null($this->actual_start_date) && is_null($this->actual_completion_date)) {
            $status = 'استمرار';
        } elseif (! is_null($this->actual_start_date) && ! is_null($this->actual_completion_date)) {
            $status = 'انتهاء';
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->price,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'period' => $this->period,
            'unit' => $this->unit,
            'actual_start_date' => $this->actual_start_date,
            'actual_completion_date' => $this->actual_completion_date,
            'status' => $status,
            'classrooms' => ClassroomResource::collection($this->classrooms),
            'subjects' => SubjectResource::collection($this->subject),
            'exams' => ExamResource::collection($this->exams),
            'scholarship' => ScholarshipResource::collection($this->scholarships),
        ];
    }
}
