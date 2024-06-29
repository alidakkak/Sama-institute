<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStudentPaymentRequest;
use App\Http\Requests\UpdateStudentPaymentRequest;
use App\Http\Resources\StudentPaymentResource;
use App\Models\StudentPayment;

class StudentPaymentController extends Controller
{
    public function index()
    {
        $studentPayment = StudentPayment::all();

        return StudentPaymentResource::collection($studentPayment);
    }

    //// API For Flutter To Get Student Payment
    public function getStudentPayment()
    {
        $student = auth('api_student')->user()->id;
        $payment = StudentPayment::where('student_id', $student)
            ->orderBy('created_at', 'desc')
            ->get();
        return StudentPaymentResource::collection($payment);
    }

    public function store(StoreStudentPaymentRequest $request)
    {
        try {
            $studentPayment = StudentPayment::create($request->all());

            return response()->json([
                'message' => 'Created SuccessFully',
                'data' => StudentPaymentResource::make($studentPayment),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(UpdateStudentPaymentRequest $request, $Id)
    {
        try {
            $studentPayment = StudentPayment::find($Id);
            if (! $studentPayment) {
                return response()->json(['message' => 'Not Found'], 404);
            }
            $studentPayment->update($request->all());

            return response()->json([
                'message' => 'Updated SuccessFully',
                'data' => StudentPaymentResource::make($studentPayment),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
