<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScholarshipResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $semesterPrice = $this->semester->price;
        $afterDiscount = $this->semester->price - $this->discount;
        $approximatelyPercentage = (($semesterPrice - $afterDiscount) / $semesterPrice) * 100;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'discount' => $this->discount,
            'approximatelyPercentage' => $approximatelyPercentage.'%',
        ];
    }
}
