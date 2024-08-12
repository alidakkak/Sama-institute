<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStudentPaymentRequest;
use App\Http\Requests\UpdateStudentPaymentRequest;
use App\Http\Resources\StudentPaymentResource;
use App\Models\DeviceToken;
use App\Models\Registration;
use App\Models\Semester;
use App\Models\Student;
use App\Models\StudentPayment;
use App\Services\FirebaseService;

class StudentPaymentController extends Controller
{
    public function index()
    {
        $studentPayment = StudentPayment::all();

        return StudentPaymentResource::collection($studentPayment);
    }

    //// API For Flutter To Get Student Payment
    public function getStudentPayment($semesterID)
    {
        $student = auth('api_student')->user()->id;
        $payment = StudentPayment::where('student_id', $student)
            ->where('semester_id', $semesterID)
            ->orderBy('created_at', 'desc')
            ->get();

        return StudentPaymentResource::collection($payment);
    }

    public function store(StoreStudentPaymentRequest $request)
    {
        try {
            $student = Student::find($request->student_id);
            $semester = Semester::find($request->semester_id);

            $registration = Registration::where('student_id', $student->id)
                ->where('semester_id', $semester->id)
                ->first();

            if (! $registration) {
                return response()->json([
                    'message' => 'Registration not found',
                ], 404);
            }
            $after_discount = $registration->after_discount;
            $financialDues = $registration->financialDues;

            $studentPayment = StudentPayment::create($request->all());

            if ($registration->scholarship_id !== null) {
                $registration->update([
                    'after_discount' => $after_discount - $request->price,
                ]);
            } else {
                $registration->update([
                    'financialDues' => $financialDues - $request->price,
                ]);
            }
            $registration->update([
                'status' => \App\Status\Student::Active,
            ]);

            $title = 'تم إضافة دفعة جديدة';
            $body = $request->title;
            /// Device Key
            $FcmToken = DeviceToken::where('student_id', $student->id)->pluck('device_token')->toArray();

            $data = [
                'title' => $request->title,
                'price' => $request->price,
            ];
            $firebaseNotification = new FirebaseService;
            $firebaseNotification->BasicSendNotification($title, $body, $FcmToken, $data);

            return response()->json([
                'message' => 'Created Successfully',
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
