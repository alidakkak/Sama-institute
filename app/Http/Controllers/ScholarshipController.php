<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreScholarshipRequest;
use App\Http\Requests\UpdateScholarshipRequest;
use App\Http\Resources\ScholarshipResource;
use App\Models\Registration;
use App\Models\Scholarship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScholarshipController extends Controller
{
    public function index()
    {
        $scholarship = Scholarship::all();

        return ScholarshipResource::collection($scholarship);
    }

    public function store(StoreScholarshipRequest $request)
    {
        DB::beginTransaction();
        try {
            $scholarship = Scholarship::create($request->all());

            if ($request->discount > $scholarship->semester->price) {
                DB::rollback();
                return response()->json(['message' => 'سعر الحسم اكبر من سعر الدورة'], 422);
            }
            DB::commit();
            return response()->json([
                'message' => 'Created SuccessFully',
                'data' => ScholarshipResource::make($scholarship),
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(UpdateScholarshipRequest $request, $scholarshipId)
    {
        try {
            $scholarship = Scholarship::find($scholarshipId);
            if (! $scholarship) {
                return response()->json(['message' => 'Not Found'], 404);
            }
            $scholarship->update($request->all());

            return response()->json([
                'message' => 'Updated SuccessFully',
                'data' => ScholarshipResource::make($scholarship),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($scholarshipId)
    {
        $scholarship = Scholarship::find($scholarshipId);
        if (! $scholarship) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return ScholarshipResource::make($scholarship);
    }

    public function delete($scholarshipId)
    {
        try {
            $scholarship = Scholarship::find($scholarshipId);
            if (! $scholarship) {
                return response()->json(['message' => 'Not Found'], 404);
            }
            $scholarship->delete();

            return response()->json([
                'message' => 'Deleted SuccessFully',
                'data' => ScholarshipResource::make($scholarship),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function specialDiscount(Request $request)
    {
        $registration = Registration::where('id', $request->registrationID)->first();
        if (! $registration) {
            return response()->json(['message' => 'Not found'], 404);
        }
        if ($request->discount > $registration->total_dues_without_decrease) {
            return response()->json(['message' => 'الحسم اكبر من المستحقات الدفع الكلية للطالب'], 422);
        }
        if ($registration->scholarship_id !== null) {
            $registration->update([
                'after_discount' => $registration->after_discount - $request->discount,
            ]);
        } else {
            $registration->update([
                'financialDues' => $registration->financialDues - $request->discount,
            ]);
        }

        return response()->json(['message' => 'تمت عملية الحسم بنجاح']);
    }
}
