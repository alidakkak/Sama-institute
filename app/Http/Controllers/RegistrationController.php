<?php

namespace App\Http\Controllers;

use App\Http\Requests\CalculateCoursePriceRequest;
use App\Http\Requests\StoreRegistrationRequest;
use App\Http\Requests\UpdateRegistrationRequest;
use App\Models\Registration;
use App\Models\Scholarship;
use App\Models\Semester;
use App\Models\Student;
use App\Models\StudentSubject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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

        // Calculate the default price for each session
        $default_price_per_session = $total_price / $total_sessions;

        // Calculate the highest and lowest price
        $highest_price = $default_price_per_session * (1 + $percentage_increase / 100);
        $lowest_price = $default_price_per_session * (1 - $percentage_increase / 100);
        // The difference between the highest and lowest price
        $price_difference = $highest_price - $lowest_price;

        $prices = [];
        $cumulative_price = 0;

        if (fmod($total_sessions, 1) == 0.5) {
            $decrease_step = $price_difference / (($total_sessions * 2) - 1);

            for ($i = 0; $i < $total_sessions * 2; $i++) {
                // Calculate the price per session
                $session_price = ($highest_price - ($decrease_step * $i)) / 2;

                // Calculate the cumulative price
                $cumulative_price += $session_price;

                // Round the cumulative price using the user-entered rounding threshold
                $rounded_price = round($cumulative_price / $rounding_threshold) * $rounding_threshold;

                $prices[] = [
                    'session_number' => ($i + 1) / 2,
                    'session_price' => round($session_price),
                    'cumulative_price' => round($cumulative_price),
                    'rounded_price' => $rounded_price,
                ];
            }
        } elseif ($total_sessions == 1) {
            $decrease_step = $price_difference / ($total_sessions);
            for ($i = 0; $i < $total_sessions; $i++) {
                $session_price = $decrease_step;

                $cumulative_price += $session_price;

                $rounded_price = round($cumulative_price / $rounding_threshold) * $rounding_threshold;

                $prices[] = [
                    'session_number' => $i + 1,
                    'session_price' => round($session_price),
                    'cumulative_price' => round($cumulative_price),
                    'rounded_price' => $rounded_price,
                ];
            }
        } else {
            $decrease_step = $price_difference / ($total_sessions - 1);
            for ($i = 0; $i < $total_sessions; $i++) {
                // Calculate the price per session
                $session_price = $highest_price - ($decrease_step * $i);

                // Calculate the cumulative price
                $cumulative_price += $session_price;

                // Round the cumulative price using the user-entered rounding threshold
                $rounded_price = round($cumulative_price / $rounding_threshold) * $rounding_threshold;

                $prices[] = [
                    'session_number' => $i + 1,
                    'session_price' => round($session_price),
                    'cumulative_price' => round($cumulative_price),
                    'rounded_price' => $rounded_price,
                ];
            }
        }

        return response()->json([
            'table' => $prices,
            'finalPrice' => $rounded_price,
            'default_price_per_session' => $default_price_per_session,
        ]);
    }

    public function store(StoreRegistrationRequest $request)
    {
        try {
            DB::beginTransaction();
            $discount = Scholarship::where('id', $request->scholarship_id)->value('discount');
            $numberOfWeeksOfTheCycle = Semester::where('id', $request->semester_id)->value('period');
            $after_discount = $request->financialDues - $discount;
            $totalDuesWithoutDecrease = $request->scholarship_id ? $after_discount : $request->financialDues;
            $amountOfDelay = $request->financialDues / $numberOfWeeksOfTheCycle;
            $priceOfDelay = $amountOfDelay * $request->amountOfDelay;
            $registration = Registration::create(array_merge(
                $request->all(),
                ['total_dues_without_decrease' => $totalDuesWithoutDecrease - $priceOfDelay,
                    'after_discount' => $after_discount - $priceOfDelay,
                    'financialDues' => $request->financialDues - $priceOfDelay,
                ]
            ));
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

    public function quickRegistration(StoreRegistrationRequest $request)
    {
        try {
            DB::beginTransaction();
            $image = $request->hasFile('image') ? $request->file('image') : '/students_image/female.jpg';
            $password = Str::random(6);
            $student = Student::create(array_merge(
                $request->only('first_name', 'last_name', 'father_name', 'phone_number'),
                [
                    'password' => Hash::make($password),
                    'image' => $image,
                ]
            ));
            $discount = Scholarship::where('id', $request->scholarship_id)->value('discount');
            $numberOfWeeksOfTheCycle = Semester::where('id', $request->semester_id)->value('period');
            $after_discount = $request->financialDues - $discount;
            $totalDuesWithoutDecrease = $request->scholarship_id ? $after_discount : $request->financialDues;
            $amountOfDelay = $request->financialDues / $numberOfWeeksOfTheCycle;
            $priceOfDelay = $amountOfDelay * $request->amountOfDelay;
            $registration = Registration::create(array_merge(
                $request->all(),
                ['total_dues_without_decrease' => $totalDuesWithoutDecrease - $priceOfDelay,
                    'after_discount' => $after_discount - $priceOfDelay,
                    'financialDues' => $request->financialDues - $priceOfDelay,
                    'student_id' => $student->id
                ]
            ));
            foreach ($request->subjects as $subject) {
                StudentSubject::create([
                    'registration_id' => $registration->id,
                    'subject_id' => $subject['subject_id'],
                    'student_id' => $student->id,
                ]);
            }
            DB::commit();

            return response()->json([
                'message' => 'Successfully Registered',
                'password' => $password
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
            $discount = Scholarship::where('id', $request->scholarship_id)->value('discount');
            $after_discount = $request->financialDues - $discount;
            $totalDuesWithoutDecrease = $request->scholarship_id ? $after_discount : $request->financialDues;

            $registration = Registration::find($id);

            if (! $registration) {
                return response()->json([
                    'message' => 'Registration not found',
                ], 404);
            }

            // Calculate student payments
            $studentPayments = $registration->student->studentPayment()
                ->where('semester_id', $request->semester_id)
                ->sum('price');

            // Calculate student ExtraCharge
            $extraCharge = $registration->student->extraCharge()
                ->where('semester_id', $request->semester_id)
                ->sum('price');

            $registration->update(array_merge(
                $request->all(),
                ['total_dues_without_decrease' => $totalDuesWithoutDecrease + $extraCharge, 'after_discount' => $after_discount,
                    'scholarship_id' => $request->scholarship_id ? $request->scholarship_id : null,
                ]
            ));
            if ($registration->scholarship_id !== null) {
                $registration->update([
                    'after_discount' => $registration->after_discount - $studentPayments + $extraCharge,
                ]);
            } else {
                $registration->update([
                    'financialDues' => $registration->financialDues - $studentPayments + $extraCharge,
                ]);
            }

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

    public function withdrawalFromTheCourse(StoreRegistrationRequest $request)
    {
        $registration = Registration::where('id', $request->registration_id)->first();
        if (! $registration) {
            return response()->json(['message' => 'Registration not found'], 404);
        }
        $semesterPrice = $registration->total_dues_without_decrease;
        $semesterPeriod = $registration->semester->period;
        $finalPrice = ($semesterPrice / $semesterPeriod) * $request->value * $request->number;
        $registration->update([
            'total_dues_without_decrease' => $finalPrice,
            'status' => \App\Status\Student::Withdrawn,
        ]);

        // Calculate student payments
        $studentPayments = $registration->student->studentPayment()
            ->where('semester_id', $registration->semester_id)
            ->sum('price');

        // Calculate student ExtraCharge
        $extraCharge = $registration->student->extraCharge()
            ->where('semester_id', $registration->semester_id)
            ->sum('price');

        if ($registration->scholarship_id !== null) {
            $registration->update([
                'after_discount' => $finalPrice - $studentPayments + $extraCharge,
            ]);
        } else {
            $registration->update([
                'financialDues' => $finalPrice - $studentPayments + $extraCharge,
            ]);
        }

        return response()->json(['message' => 'Successfully Withdrawn']);
    }
}
