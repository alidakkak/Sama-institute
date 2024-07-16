<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RegistrationResource extends JsonResource
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
            'semester_id' => $this->semester_id,
            'semesterName' => $this->semester->name,
            'totalPrice' => $this->total_dues_without_decrease,
            'TheRemainingAmountOf' => $this->scholarship_id !== null
                ? $this->after_discount
                : $this->financialDues,
            'studentPayments' => StudentPaymentResource::collection($this->studentPayments),
            'extraCharges' => ExtraChargeResource::collection($this->extraCharges),
        ];
    }
}
