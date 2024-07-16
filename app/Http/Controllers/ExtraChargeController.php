<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExtraChargeRequest;
use App\Http\Requests\UpdateExtraChargeRequest;
use App\Http\Resources\ExtraChargeResource;
use App\Models\ExtraCharge;
use Illuminate\Support\Facades\Auth;

class ExtraChargeController extends Controller
{
    public function index()
    {
        $extraCharge = ExtraCharge::all();

        return ExtraChargeResource::collection($extraCharge);
    }

    /// API For Flutter To Get ExtraCharge
    public function getExtraCharge()
    {
        $studentID = auth::guard('api_student')->user()->id;
        $extraCharge = ExtraCharge::where('student_id', $studentID)
            ->orderBy('created_at', 'desc')
            ->get();

        return ExtraChargeResource::collection($extraCharge);
    }

    public function store(StoreExtraChargeRequest $request)
    {
        try {
            $extraCharge = ExtraCharge::create($request->all());

            return response()->json([
                'message' => 'Created SuccessFully',
                'data' => ExtraChargeResource::make($extraCharge),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(UpdateExtraChargeRequest $request, $extraChargeId)
    {
        try {
            $extraCharge = ExtraCharge::find($extraChargeId);
            if (! $extraCharge) {
                return response()->json(['message' => 'Not Found'], 404);
            }
            $extraCharge->update($request->all());

            return response()->json([
                'message' => 'Updated SuccessFully',
                'data' => ExtraChargeResource::make($extraCharge),
            ]);
        } catch (\Exception $e) {
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
