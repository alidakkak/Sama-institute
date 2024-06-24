<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    public $token;

    public function __construct($resource, $token = null)
    {
        parent::__construct($resource);
        $this->token = $token;
    }

    public function toArray(Request $request): array
    {
        if ($request->route()->uri() === 'api/showDetails/{studentID}') {
            $subjectResults = $this->marks->groupBy('subject_id')
                ->map(function ($subjectGroup) {
                    $examResults = $subjectGroup->groupBy('exam_id')
                        ->map(function ($examGroup) {
                            return [
                                'examName' => $examGroup->first()->exam->name,
                                'average' => $examGroup->avg('result'),
                            ];
                        });

                    return [
                        'subjectName' => $subjectGroup->first()->subject->name,
                        'average' => $subjectGroup->avg('result'),
                        'exams' => $examResults->values()->all(),
                    ];
                });

            return [
                'details' => ShowDetailsResource::collection($this->marks),
                'subjectResults' => $subjectResults->values()->all(),
            ];
        }

        if ($request->route()->uri() === 'api/getInfoStudent') {
            return [
                'id' => $this->id,
                'name' => $this->name,
                'user_name' => $this->user_name,
                'classroom' => $this->classrooms->pluck('name'),
            ];
        }

        $data = [
            'id' => $this->id,
            'user_name' => $this->user_name,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'age' => $this->age,
            'date_of_birth' => $this->date_of_birth,
            'place_of_birth' => $this->place_of_birth,
            'gender' => $this->gender,
            'marital_status' => $this->marital_status,
            'previous_educational_status' => $this->previous_educational_status,
            'phone_number' => $this->phone_number,
            'telephone_number' => $this->telephone_number,
            'facebook' => $this->facebook,
            'instagram' => $this->instagram,
            'location' => $this->location,
            'father_name' => $this->father_name,
            'father_work' => $this->father_work,
            'father_of_birth' => $this->father_of_birth,
            'mother_name' => $this->mother_name,
            'mother_work' => $this->mother_work,
            'mother_of_birth' => $this->mother_of_birth,
            'other_name' => $this->other_name,
            'other_work' => $this->other_work,
            'other_of_birth' => $this->other_of_birth,
            'note1' => $this->note1,
            'note2' => $this->note2,
            'image' => $this->image,
        ];

        if ($this->token) {
            $data['token'] = $this->token;
        }

        return $data;
    }
}
