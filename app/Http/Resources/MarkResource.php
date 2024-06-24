<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MarkResource extends JsonResource
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
            'studentName' => $this->student->name,
            'studentID' => $this->student->id,
            'subjectName' => $this->subject->name,
            'examName' => $this->exam->name,
            'result' => $this->result,
        ];
    }
}
