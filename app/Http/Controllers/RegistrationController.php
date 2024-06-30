<?php

namespace App\Http\Controllers;

use App\Http\Requests\CalculateCoursePriceRequest;
use App\Http\Requests\StoreRegistrationRequest;
use App\Http\Resources\RegistrationResource;
use App\Models\Registration;
use App\Models\Scholarship;
use App\Models\StudentSubject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegistrationController extends Controller
{
    public function calculateCoursePrice(CalculateCoursePriceRequest $request)
    {
        $total_sessions = $request->total_number_of_sessions;
        $total_price = $request->price;
        $percentage_increase = $request->percentage_increase_over_the_default_price;
        $rounding_threshold = $request->rounding_threshold;
        $scholarship = Scholarship::where('id', $request->scholarship_id)->value('discount');

        // حساب السعر الافتراضي لكل جلسة
        $default_price_per_session = $total_price / $total_sessions;

        // حساب أعلى وأقل سعر
        $highest_price = $default_price_per_session * (1 + $percentage_increase / 100);
        $lowest_price = $default_price_per_session * (1 - $percentage_increase / 100);

        // الفرق بين أعلى وأقل سعر
        $price_difference = $highest_price - $lowest_price;
        $decrease_step = $price_difference / ($total_sessions - 1);

        $prices = [];
        $cumulative_price = 0;

        for ($i = 0; $i < $total_sessions; $i++) {
            // حساب السعر لكل جلسة
            $session_price = $highest_price - ($decrease_step * $i);

            // حساب السعر التراكمي
            $cumulative_price += $session_price;

            // تقريب السعر التراكمي باستخدام عتبة التقريب التي أدخلها المستخدم
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
            $registration = Registration::create(array_merge([
                $request->all(),
                'after_discount'
            ]));
            foreach ($request->subjects as $subject) {
                StudentSubject::create([
                    'subject_id' => $subject->id,
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
}
