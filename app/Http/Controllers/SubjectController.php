<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSubjectRequest;
use App\Http\Requests\UpdateSubjectRequest;
use App\Http\Resources\SubjectResource;
use App\Models\Exam;
use App\Models\Mark;
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
            ->get();

        $totalWeightedMarks = 0;
        $totalPercent = 0;

        foreach ($resultMarks as $mark) {
            $examPercent = Exam::where('id', $mark->exam_id)->value('percent');

            $totalWeightedMarks += $mark->result * ($examPercent / 100);

            $totalPercent += $examPercent;
        }

        if ($totalPercent > 0) {
            $GPA = $totalWeightedMarks / ($totalPercent / 100);
        } else {
            $GPA = 0;
        }

        return response()->json(['GPA' => round($GPA, 2)]);
    }

    public function delete($subjectId)
    {
        try {
            $subject = Subject::find($subjectId);
            if (! $subject) {
                return response()->json(['message' => 'Not Found'], 404);
            }
            $subject->delete();

            return response()->json([
                'message' => 'Deleted SuccessFully',
                'data' => SubjectResource::make($subject),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
