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
     $theRemainingAmountOf =   $this->scholarship_id !== null
            ? $this->after_discount
            : $this->financialDues;
        return [
            'id' => $this->id,
            'semester_id' => $this->semester_id,
            'semesterName' => $this->semester->name,
            'scholarship' => ScholarshipResource::make($this->scholarship),
            'totalPrice' => $this->total_dues_without_decrease,
            'theRemainingAmountOf' => $theRemainingAmountOf,
            'theAmountThatWasPaid' => $this->total_dues_without_decrease - $theRemainingAmountOf,
            'studentPayments' => StudentPaymentResource::collection($this->studentPayments),
            'extraCharges' => ExtraChargeResource::collection($this->extraCharges),
        ];
    }
}
