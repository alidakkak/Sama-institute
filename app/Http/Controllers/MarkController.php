<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMarkRequest;
use App\Http\Requests\UpdateMarkRequest;
use App\Http\Resources\MarkResource;
use App\Models\Exam;
use App\Models\Mark;
use App\Models\Notification;
use App\Models\Registration;
use App\Models\Semester;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class MarkController extends Controller
{
    /// API For Flutter
    public function getMarks($semesterID)
    {
        $studentID = auth::guard('api_student')->user()->id;
        $marks = Mark::where('student_id', $studentID)
            ->where('semester_id', $semesterID)
            ->orderBy('created_at', 'desc')
            ->get();

        return MarkResource::collection($marks);
    }

    public function store(StoreMarkRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            $notificationsData = [];

            // جلب العلامات الحالية وتخزين العلامات الجديدة
            $allMarks = Mark::whereIn('student_id', $data['student_id'])
                ->where('semester_id', $data['semester_id'])
                ->get()
                ->groupBy('student_id');

            foreach ($data['student_id'] as $index => $student_id) {
                $mark = Mark::create([
                    'student_id' => $student_id,
                    'subject_id' => $data['subject_id'],
                    'semester_id' => $data['semester_id'],
                    'date' => $data['date'],
                    'exam_id' => $data['exam_id'],
                    'result' => $data['result'][$index],
                ]);

                if (isset($allMarks[$student_id])) {
                    $allMarks[$student_id][] = $mark;
                } else {
                    $allMarks[$student_id] = [$mark];
                }

                try {
                    $FcmTokenResponse = Http::get('https://api.dev2.gomaplus.tech/api/getFcmTokensFromServer', [
                        'student_id' => $mark->student_id,
                    ]);

                    if ($FcmTokenResponse->successful()) {
                        $FcmToken = $FcmTokenResponse->json();
                    } else {
                        throw new \Exception('Failed to retrieve FCM tokens');
                    }
                } catch (\Exception $e) {
                    Notification::create([
                        'student_id' => $mark->student_id,
                        'title' => 'تم إضافة علامة جديدة',
                        'body' => 'علامة مادة: '.$mark->subject->name.' تم إضافتها.',
                        'data' => json_encode([
                            'type' => 'mark',
                            'result' => $mark->result,
                            'date' => $mark->date,
                            'status' => $mark->result >= 40 ? 'ناجح' : 'راسب',
                        ]),
                    ]);

                    continue;
                }

                $notificationsData[] = [
                    'title' => 'تم إضافة علامة جديدة',
                    'body' => 'علامة مادة: '.$mark->subject->name.' تم إضافتها.',
                    'data' => [
                        'type' => 'mark',
                        'result' => $mark->result,
                        'date' => $mark->date,
                        'status' => $mark->result >= 40 ? 'ناجح' : 'راسب',
                    ],
                    'tokens' => $FcmToken,
                    'student_id' => $mark->student_id,
                ];
            }

            // حساب الـ GPA وتحديثه في الـ Registration
            foreach ($allMarks as $student_id => $marks) {
                $totalWeightedMarks = 0;
                $totalPercent = 0;

                foreach ($marks as $mark) {
                    $examPercent = Exam::where('id', $mark->exam_id)->value('percent');
                    $totalWeightedMarks += $mark->result * ($examPercent / 100);
                    $totalPercent += $examPercent;
                }

                if ($totalPercent > 0) {
                    $GPA = $totalWeightedMarks / ($totalPercent / 100);
                } else {
                    $GPA = 0;
                }

                Registration::where('student_id', $student_id)
                    ->where('semester_id', $data['semester_id'])
                    ->update(['GPA' => round($GPA, 2)]);
            }

            DB::commit();

            $firebaseNotification = new FirebaseService;
            foreach ($notificationsData as $notification) {
                try {
                    $firebaseNotification->BasicSendNotification(
                        $notification['title'],
                        $notification['body'],
                        $notification['tokens'],
                        $notification['data']
                    );
                } catch (\Exception $e) {
                    Notification::create([
                        'student_id' => $notification['student_id'],
                        'title' => $notification['title'],
                        'body' => $notification['body'],
                        'data' => json_encode($notification['data']),
                    ]);
                }
            }

            return response()->json([
                'message' => 'تم الإنشاء بنجاح',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'حدث خطأ',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(UpdateMarkRequest $request, $markId)
    {
        DB::beginTransaction();
        try {
            $mark = Mark::find($markId);
            if (! $mark) {
                return response()->json(['message' => 'Not Found'], 404);
            }

            $mark->update($request->only('result'));

            $allMarks = Mark::where('student_id', $mark->student_id)
                ->where('semester_id', $mark->semester_id)
                ->get();

            $totalWeightedMarks = 0;
            $totalPercent = 0;

            foreach ($allMarks as $studentMark) {
                $examPercent = Exam::where('id', $studentMark->exam_id)->value('percent');
                $totalWeightedMarks += $studentMark->result * ($examPercent / 100);
                $totalPercent += $examPercent;
            }

            if ($totalPercent > 0) {
                $GPA = $totalWeightedMarks / ($totalPercent / 100);
            } else {
                $GPA = 0;
            }

            Registration::where('student_id', $mark->student_id)
                ->where('semester_id', $mark->semester_id)
                ->update(['GPA' => round($GPA, 2)]);

            DB::commit();

            return response()->json([
                'message' => 'Updated Successfully',
                'data' => MarkResource::make($mark),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    //// Get Student By SemesterID, SubjectID, ExamID
    public function showStudent(StoreMarkRequest $request)
    {
        $semesterID = $request->input('semester_id');
        $subjectID = $request->input('subject_id');
        $examID = $request->input('exam_id');

        $semester = Semester::findOrFail($semesterID);

        $students = $semester->registrations()
            ->whereHas('student.subjects', function ($query) use ($subjectID) {
                $query->where('subject_id', $subjectID);
            })
            ->whereDoesntHave('student.marks', function ($query) use ($subjectID, $examID, $semesterID) {
                $query->where('subject_id', $subjectID)
                    ->where('exam_id', $examID)
                    ->where('semester_id', $semesterID);
            })
            ->with('student')
            ->get()
            ->map(function ($registration) {
                $student = $registration->student;

                return [
                    'id' => $student->id,
                    'full_name' => $student->first_name.' '.$student->last_name,
                ];
            });

        return response()->json(['student' => $students], 200);
    }

    public function delete($markId)
    {
        try {
            $mark = Mark::find($markId);
            if (! $mark) {
                return response()->json(['message' => 'Not Found'], 404);
            }
            $mark->delete();

            return response()->json([
                'message' => 'Deleted SuccessFully',
                'data' => MarkResource::make($mark),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
