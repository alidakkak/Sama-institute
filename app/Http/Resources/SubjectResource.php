<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $teachers = $this->teachers->map(function ($teacher) {
            return $teacher->first_name . ' ' . $teacher->last_name;
        })->implode(', ');

        return [
            'id' => $this->id,
            'name' => $this->name,
            'number_sessions_per_week' => $this->number_sessions_per_week,
            'teacherName' => $teachers !== '' ? $teachers : null,
        ];
    }
}
