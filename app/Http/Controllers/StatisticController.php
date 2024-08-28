<?php

namespace App\Http\Controllers;

use App\Models\ExtraCharge;
use App\Models\GeneralExpense;
use App\Models\Registration;
use App\Models\Semester;
use App\Models\Student;
use App\Models\StudentPayment;
use App\Models\Teacher;
use App\Models\TeacherSalary;

class StatisticController extends Controller
{
    public function getStatisticGeneral()
    {
        $registrations = Registration::with('student')->get();
        $allStudents = Student::count();

        $semesters = Semester::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->keyBy('status')
            ->map(fn ($item) => $item->count);

        $allSemester = $semesters->sum();
        $waitingSemester = $semesters->get(\App\Status\Semester::waiting, 0);
        $continuationSemester = $semesters->get(\App\Status\Semester::continuation, 0);
        $endSemester = $semesters->get(\App\Status\Semester::end, 0);

        $teachers = Teacher::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->keyBy('status')
            ->map(fn ($item) => $item->count);

        $allTeachers = $teachers->sum();
        $activeTeacher = $teachers->get('active', 0);
        $inactiveTeacher = $teachers->get('inactive', 0);

        $studentStatuses = $registrations->groupBy('status')->map->count();

        $statistics = [
            'teachers' => $allTeachers,
            'activeTeacher' => $activeTeacher,
            'inactiveTeacher' => $inactiveTeacher,
            'allSemester' => $allSemester,
            'waitingSemester' => $waitingSemester,
            'continuationSemester' => $continuationSemester,
            'endSemester' => $endSemester,
            'allRegistrations' => $registrations->count(),
            'allStudents' => $allStudents,
            'activeStudents' => $studentStatuses->get(\App\Status\Student::Active, 0),
            'absentStudents' => $studentStatuses->get(\App\Status\Student::Absent, 0),
            'withdrawnStudents' => $studentStatuses->get(\App\Status\Student::Withdrawn, 0),
        ];

        return response()->json($statistics);
    }

    public function financialResults()
    {
        // إجمالي الرواتب المدفوعة للمعلمين
        $totalTeacherSalary = TeacherSalary::all()->sum('price');

        // إجمالي المصروفات العامة
        $totalGeneralExpense = GeneralExpense::all()->sum('price');

        // إجمالي المدفوعات المستلمة من الطلاب
        $totalStudentPayments = StudentPayment::all()->sum('price');

        $extraCharge = ExtraCharge::all()->sum('price');

        // إجمالي النفقات (الرواتب والمصروفات العامة)
        $totalExpenses = $totalTeacherSalary + $totalGeneralExpense;

        // صافي الأرباح أو الخسائر
        $netProfitOrLoss = ($totalStudentPayments + $extraCharge) - $totalExpenses;

        return response()->json([
            'totalTeacherSalary' => $totalTeacherSalary,
            'totalGeneralExpense' => $totalGeneralExpense,
            'totalStudentPayments' => $totalStudentPayments,
            'extraCharge' => $extraCharge,
            'totalExpenses' => $totalExpenses,
            'netProfitOrLoss' => $netProfitOrLoss,
        ]);
    }
}
