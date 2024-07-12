<?php

namespace App\Http\Controllers;

use App\Http\Requests\CalculateCoursePriceRequest;
use App\Http\Requests\StoreRegistrationRequest;
use App\Http\Requests\UpdateRegistrationRequest;
use App\Models\Registration;
use App\Models\Scholarship;
use App\Models\Student;
use App\Models\StudentSubject;
use Illuminate\Support\Facades\DB;

class RegistrationController extends Controller
{
    /*    public function processRequest(Request $request)
        {
            $action = $request->input('action');

            if ($action === 'calculate') {
                return $this->calculateCoursePrice(new CalculateCoursePriceRequest($request->all()));
            } elseif ($action === 'save') {
                return $this->store(new StoreRegistrationRequest($request->all()));
            } else {
                return response()->json([
                    'message' => 'Invalid action',
                ], 400);
            }
        }*/

    public function calculateCoursePrice(CalculateCoursePriceRequest $request)
    {
        $total_sessions = $request->total_number_of_sessions;
        $total_price = $request->price;
        $percentage_increase = $request->percentage_increase_over_the_default_price;
        $rounding_threshold = $request->rounding_threshold;
        $scholarship = Scholarship::where('id', $request->scholarship_id)->value('discount');

        // Calculate the default price for each session
        $default_price_per_session = $total_price / $total_sessions;

        // Calculate the highest and lowest price
        $highest_price = $default_price_per_session * (1 + $percentage_increase / 100);
        $lowest_price = $default_price_per_session * (1 - $percentage_increase / 100);

        // The difference between the highest and lowest price
        $price_difference = $highest_price - $lowest_price;
        $decrease_step = $price_difference / ($total_sessions - 1);

        $prices = [];
        $cumulative_price = 0;

        for ($i = 0; $i < $total_sessions; $i++) {
            // Calculate the price per session
            $session_price = $highest_price - ($decrease_step * $i);

            // Calculate the cumulative price
            $cumulative_price += $session_price;

            // Round the cumulative price using the user-entered rounding threshold
            $rounded_price = round($cumulative_price / $rounding_threshold) * $rounding_threshold;

            $prices[] = [
                'session_number' => $i + 1,
                'session_price' => round($session_price, 0),
                'cumulative_price' => round($cumulative_price),
                'rounded_price' => $rounded_price,
            ];
        }

        return response()->json([
            'table' => $prices,
            'finalPrice' => $rounded_price,
            'afterDiscount' => $rounded_price * (1 - $scholarship / 100),
            'default_price_per_session' => $default_price_per_session,
        ]);
    }

    public function store(StoreRegistrationRequest $request)
    {
        try {
            DB::beginTransaction();
            $registration = Registration::create($request->all());
            foreach ($request->subjects as $subject) {
                StudentSubject::create([
                    'registration_id' => $registration->id,
                    'subject_id' => $subject['subject_id'],
                    'student_id' => $request->student_id,
                ]);
            }
            DB::commit();

            return response()->json([
                'message' => 'Successfully Registered',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(UpdateRegistrationRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $registration = Registration::find($id);

            $studentPayments = $registration->student->studentPayment(function ($quray) {
                return $quray->where('semester_id', $request->semester_id);
            })
                ->sum('price');
            return $studentPayments;

            if (!$registration) {
                return response()->json([
                    'message' => 'Registration not found',
                ], 404);
            }

            $registration->update($request->all());

            StudentSubject::where('registration_id', $id)->delete();

            foreach ($request->subjects as $subject) {
                StudentSubject::create([
                    'registration_id' => $registration->id,
                    'subject_id' => $subject['subject_id'],
                    'student_id' => $request->student_id,
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Successfully Updated',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}
