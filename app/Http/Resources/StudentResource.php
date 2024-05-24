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
                'name' => $this->name,
                'user_name' => $this->user_name,
                'classroom' => $this->classrooms->pluck('name')
            ];
        }

        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'user_name' => $this->user_name,
        ];

        if ($this->token) {
            $data['password'] = $this->token;
        }

        return $data;
    }
}
