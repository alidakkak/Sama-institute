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
        //// For Flutter
        if ($request->route()->uri() === 'api/getInfoStudent') {
            return [
                'id' => $this->id,
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'phone_number' => $this->phone_number,
                'image' => url($this->image),
                'Registration' => RegistrationResource::collection($this->registrations),
            ];
        }

        $data = [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            // 'age' => $this->age,
            'date_of_birth' => $this->date_of_birth,
            'place_of_birth' => $this->place_of_birth,
            //  'gender' => $this->gender,
            'marital_status' => $this->marital_status,
            'previous_educational_status' => $this->previous_educational_status,
            'phone_number' => $this->phone_number,
            'student_phone_number' => $this->student_phone_number,
            'telephone_number' => $this->telephone_number,
            'facebook' => $this->facebook,
            // 'instagram' => $this->instagram,
            'location' => $this->location,
            'father_name' => $this->father_name,
            'father_work' => $this->father_work,
            'father_of_birth' => $this->father_of_birth,
            'father_Healthy' => $this->father_Healthy,
            'mother_name' => $this->mother_name,
            'mother_work' => $this->mother_work,
            'mother_of_birth' => $this->mother_of_birth,
            'mother_Healthy' => $this->mother_Healthy,
            'other_name' => $this->other_name,
            'other_work' => $this->other_work,
            'other_of_birth' => $this->other_of_birth,
            'other_Healthy' => $this->other_Healthy,
            'note' => $this->note,
            'image' => url($this->image),
            'created_at' => $this->created_at->format('Y-m-d'),
            'Registration' => RegistrationResource::collection($this->registrations),
        ];

        if ($this->token) {
            $data['token'] = $this->token;
        }

        return $data;
    }
}
