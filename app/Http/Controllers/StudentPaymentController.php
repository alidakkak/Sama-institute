<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStudentPaymentRequest;
use App\Http\Requests\UpdateStudentPaymentRequest;
use App\Http\Resources\StudentPaymentResource;
use App\Models\Notification;
use App\Models\Registration;
use App\Models\Semester;
use App\Models\Student;
use App\Models\StudentPayment;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

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
        DB::beginTransaction();
        try {
            $student = Student::find($request->student_id);
            $semester = Semester::find($request->semester_id);

            $registration = Registration::where('student_id', $student->id)
                ->where('semester_id', $semester->id)
                ->first();

            if (! $registration) {
                return response()->json([
                    'message' => 'الطالب غير مسجل في الدورة',
                ], 404);
            }
            $after_discount = $registration->after_discount;
            $financialDues = $registration->financialDues;

            if ($after_discount < $request->price || $financialDues < $request->price) {
                return response()->json(['message' => 'المبلغ المدفوع اكبر من المستحقات'], 400);
            }

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
            $data = [
                'type' => 'payment',
                'title' => $request->title,
                'price' => $request->price,
            ];

            try {
                $FcmToken = Http::get('https://api.dev2.gomaplus.tech/api/getFcmTokensFromServer', [
                    'student_id' => $studentPayment->student_id,
                ]);

                $firebaseNotification = new FirebaseService;

                $firebaseNotification->BasicSendNotification($title, $body, $FcmToken->json(), $data);
            } catch (\Exception $e) {
                Notification::create([
                    'student_id' => $studentPayment->student_id,
                    'title' => $title,
                    'body' => $body,
                    'data' => json_encode($data),
                ]);
            }
            DB::commit();

            return response()->json([
                'message' => 'Created Successfully',
                'data' => StudentPaymentResource::make($studentPayment),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(UpdateStudentPaymentRequest $request, $Id)
    {
        try {
            DB::beginTransaction();

            $studentPayment = StudentPayment::find($Id);
            if (! $studentPayment) {
                return response()->json(['message' => 'الدفعة غير موجودة'], 404);
            }

            $oldPrice = $studentPayment->price;

            $studentPayment->update($request->all());

            $registration = Registration::where('student_id', $request->student_id)
                ->where('semester_id', $request->semester_id)
                ->first();

            if (! $registration) {
                DB::rollback();

                return response()->json(['message' => 'الطالب غير مسجل في الدورة'], 404);
            }

            $newPrice = $request->price;

            if ($registration->scholarship_id !== null) {
                $updates['after_discount'] = $registration->after_discount + $oldPrice - $newPrice;
            } else {
                $updates['financialDues'] = $registration->financialDues + $oldPrice - $newPrice;
            }

            $registration->update($updates);

            DB::commit();

            return response()->json([
                'message' => 'تم التحديث بنجاح',
                'data' => StudentPaymentResource::make($studentPayment),
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'message' => 'حدث خطأ أثناء التحديث',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
