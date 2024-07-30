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
        $subjectResults = $this->marks->where('student_id', $this->student_id)->groupBy('subject_id')
            ->map(function ($subjectGroup) {
                $totalWeight = $subjectGroup->sum(function ($mark) {
                    return $mark->exam->percent;
                });

                $weightedSum = $subjectGroup->reduce(function ($carry, $mark) {
                    return $carry + $mark->result * ($mark->exam->percent / 100);
                }, 0);

                $weightedAverage = ($totalWeight > 0) ? ($weightedSum / $totalWeight) * 100 : 0;

                $examResults = $subjectGroup->groupBy('exam_id')
                    ->map(function ($examGroup) {
                        return [
                            'examName' => $examGroup->first()->exam->name,
                            'average' => $examGroup->avg('result'),
                            'weight' => $examGroup->first()->exam->percent,
                        ];
                    });

                return [
                    'subjectID' => $subjectGroup->first()->subject->id,
                    'subjectName' => $subjectGroup->first()->subject->name,
                    'average' => $weightedAverage,
                    'exams' => $examResults->values()->all(),
                ];
            });

        $totalWeightedSum = $subjectResults->sum('average');
        $subjectCount = $subjectResults->count();

        $totalGPAForAllSubjects = ($subjectCount > 0) ? ($totalWeightedSum / $subjectCount) : 0;

        $theRemainingAmountOf = $this->scholarship_id !== null
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
            'studentPayments' => StudentPaymentResource::collection($this->studentPayments->where('student_id', $this->student_id)),
            'extraCharges' => ExtraChargeResource::collection($this->extraCharges->where('student_id', $this->student_id)),
            'studentBehavior' => NoteResource::collection($this->notes->where('student_id', $this->student_id)),
            'marks' => ShowDetailsResource::collection($this->marks->where('student_id', $this->student_id)),
            'subjectResults' => $subjectResults->values()->all(),
            'total_GPA_For_All_Subjects' => $totalGPAForAllSubjects,
        ];
    }
}
