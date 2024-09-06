<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExtraChargeRequest;
use App\Http\Requests\UpdateExtraChargeRequest;
use App\Http\Resources\ExtraChargeResource;
use App\Models\ExtraCharge;
use App\Models\Registration;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExtraChargeController extends Controller
{
    public function index()
    {
        $extraCharge = ExtraCharge::all();

        return ExtraChargeResource::collection($extraCharge);
    }

    /// API For Flutter To Get ExtraCharge
    public function getExtraCharge($semesterID)
    {
        $studentID = auth::guard('api_student')->user()->id;
        $extraCharge = ExtraCharge::where('student_id', $studentID)
            ->where('semester_id', $semesterID)
            ->orderBy('created_at', 'desc')
            ->get();

        return ExtraChargeResource::collection($extraCharge);
    }

    public function store(StoreExtraChargeRequest $request)
    {
        try {
            DB::beginTransaction();
            ExtraCharge::create($request->all());
            $registration = Registration::where('student_id', $request->student_id)
                ->where('semester_id', $request->semester_id)->first();
            if (! $registration) {
                DB::rollback();

                return response()->json(['message' => 'الطالب غير مسجل في الدورة'], 404);
            }
            $registration->update([
                'total_dues_without_decrease' => $registration->total_dues_without_decrease + $request->price,
            ]);
            if ($registration->scholarship_id !== null) {
                $registration->update([
                    'after_discount' => $registration->after_discount + $request->price,
                ]);
            } else {
                $registration->update([
                    'financialDues' => $registration->financialDues + $request->price,
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Created SuccessFully',
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(UpdateExtraChargeRequest $request, $extraChargeId)
    {
        try {
            DB::beginTransaction();

            $extraCharge = ExtraCharge::find($extraChargeId);
            if (! $extraCharge) {
                return response()->json(['message' => 'Not Found'], 404);
            }

            $oldPrice = $extraCharge->price;

            $extraCharge->update($request->all());

            $registration = Registration::where('student_id', $request->student_id)
                ->where('semester_id', $request->semester_id)->first();

            if (! $registration) {
                DB::rollback();
                return response()->json(['message' => 'الطالب غير مسجل في الدورة'], 404);
            }

            $registration->update([
                'total_dues_without_decrease' => $registration->total_dues_without_decrease - $oldPrice,
            ]);

            if ($registration->scholarship_id !== null) {
                $registration->update([
                    'after_discount' => $registration->after_discount - $oldPrice,
                ]);
            } else {
                $registration->update([
                    'financialDues' => $registration->financialDues - $oldPrice,
                ]);
            }

            $newPrice = $request->price;
            $registration->update([
                'total_dues_without_decrease' => $registration->total_dues_without_decrease + $newPrice,
            ]);

            if ($registration->scholarship_id !== null) {
                $registration->update([
                    'after_discount' => $registration->after_discount + $newPrice,
                ]);
            } else {
                $registration->update([
                    'financialDues' => $registration->financialDues + $newPrice,
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Updated Successfully',
                'data' => ExtraChargeResource::make($extraCharge),
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function show($extraChargeId)
    {
        $extraCharge = ExtraCharge::find($extraChargeId);
        if (! $extraCharge) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return ExtraChargeResource::make($extraCharge);
    }

    public function delete($extraChargeId)
    {
        try {
            $extraCharge = ExtraCharge::find($extraChargeId);
            if (! $extraCharge) {
                return response()->json(['message' => 'Not Found'], 404);
            }
            $extraCharge->delete();

            return response()->json([
                'message' => 'Deleted SuccessFully',
                'data' => ExtraChargeResource::make($extraCharge),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
