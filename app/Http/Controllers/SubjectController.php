<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSubjectRequest;
use App\Http\Requests\UpdateSubjectRequest;
use App\Http\Resources\SubjectResource;
use App\Models\Exam;
use App\Models\Mark;
use App\Models\Registration;
use App\Models\StudentSubject;
use App\Models\Subject;
use Illuminate\Support\Facades\Auth;

class SubjectController extends Controller
{
    public function index()
    {
        $subject = Subject::all();

        return SubjectResource::collection($subject);
    }

    /// API For Flutter To Get Subject
    public function getSubject($semesterID)
    {
        $studentID = auth::guard('api_student')->user()->id;
        $subjectIDs = StudentSubject::where('student_id', $studentID)->pluck('subject_id');
        $subjects = Subject::whereIn('id', $subjectIDs)
            ->where('semester_id', $semesterID)
            ->get();

        return SubjectResource::collection($subjects);
    }

    /// API For Flutter To Get GPASubject
    public function GPASubject($subjectID)
    {
        $studentID = auth::guard('api_student')->user()->id;

        $resultMarks = Mark::where('subject_id', $subjectID)
            ->where('student_id', $studentID)
            ->get()
            ->groupBy('exam_id');

        $totalWeightedMarks = 0;
        $totalPercent = 0;
        $marksWithExamNames = [];

        foreach ($resultMarks as $examId => $marks) {
            $examPercent = $marks->first()->exam->percent;
            $averageMark = $marks->avg('result');
            $marksCount = $marks->count();

            $totalWeightedMarks += $averageMark * ($examPercent / 100);
            $totalPercent += $examPercent;

            $marksWithExamNames[] = [
                'result' => round($averageMark, 2),
                'date' => $marks->first()->date,
                'exam_name' => $marks->first()->exam->name,
                'percent' => $examPercent,
                'semester_id' => $marks->first()->semester_id,
                'count' => $marksCount,
            ];
        }

        // حساب GPA
        if ($totalPercent > 0) {
            $GPA = $totalWeightedMarks / ($totalPercent / 100);
        } else {
            $GPA = 0;
        }

        return response()->json([
            'GPA' => round($GPA, 2),
            'marks' => $marksWithExamNames
        ]);
    }


    public function OverallGPA($semesterID)
    {
        $studentID = auth::guard('api_student')->user()->id;

        // جلب المعدل التراكمي (GPA) من جدول registrations مباشرة
        $GPA = Registration::where('student_id', $studentID)
            ->where('semester_id', $semesterID)
            ->sum('GPA');

        // جلب نتائج المواد
        $subjectResults = Mark::where('student_id', $studentID)
            ->where('semester_id', $semesterID)
            ->get()
            ->groupBy('subject_id')
            ->map(function ($subjectGroup) {
                $totalWeight = $subjectGroup->sum(function ($mark) {
                    return $mark->exam->percent;
                });

                $weightedSum = $subjectGroup->reduce(function ($carry, $mark) {
                    return $carry + $mark->result * ($mark->exam->percent / 100);
                }, 0);

                $weightedAverage = ($totalWeight > 0) ? ($weightedSum / $totalWeight) * 100 : 0;

                return [
                    'subjectID' => $subjectGroup->first()->subject->id,
                    'subjectName' => $subjectGroup->first()->subject->name,
                    'average' => round($weightedAverage, 2),
                ];
            });

        // حساب ترتيب الطالب
        $allGPAs = Registration::where('semester_id', $semesterID)
            ->orderBy('GPA', 'desc')
            ->pluck('GPA');

        $rank = $allGPAs->search($GPA) + 1;

        return response()->json([
            'totalGPA' => round($GPA, 2),
            'rank' => $rank,
            'subjects' => $subjectResults->values()->all(),
        ]);
    }



}
