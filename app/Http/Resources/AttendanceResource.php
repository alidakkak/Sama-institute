<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceResource extends JsonResource
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
            'in_time' => Carbon::parse($this->in_time)->format('Y-m-d H:i:s'),
            'out_time' => Carbon::parse($this->out_time)->format('Y-m-d H:i:s'),
        ];
    }
}
