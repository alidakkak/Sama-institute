<?php

namespace App\Http\Controllers;

use App\Models\GeneralExpense;
use App\Models\Registration;
use App\Models\Semester;
use App\Models\Student;
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
        $teacher = TeacherSalary::all()->sum('price');
        $generalExpense = GeneralExpense::all()->sum('price');
        return $teacher;
    }
}
