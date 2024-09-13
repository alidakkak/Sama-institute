<?php

namespace App\Http\Controllers;

use App\Http\Resources\AttendanceResource;
use App\Models\ImportLog;
use App\Models\InOutLog;
use App\Models\Notification;
use App\Models\Student;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Jmrashed\Zkteco\Lib\ZKTeco;

class AttendanceController extends Controller
{
    public function fetchAttendance()
    {
        $zk = new ZKTeco('192.168.1.201');

        if ($zk->connect()) {
            $attendances = $zk->getAttendance();

            $lastImport = ImportLog::orderBy('last_import_time', 'desc')->first();
            $lastImportTime = $lastImport ? $lastImport->last_import_time : null;

            $uids = array_column($attendances, 'uid');
            $students = Student::whereIn('device_user_id', $uids)->get()->keyBy('device_user_id');
            $logs = InOutLog::whereIn('student_id', $students->pluck('id'))
                ->whereDate('in_time', now()->toDateString())
                ->get()
                ->keyBy('student_id');

            foreach ($attendances as $attendance) {
                $attendanceTime = date('Y-m-d H:i:s', strtotime($attendance['timestamp']));

                if ($lastImportTime === null || $attendanceTime > $lastImportTime) {
                    $student = $students[$attendance['uid']] ?? null;

                    if ($student) {
                        $log = $logs[$student->id] ?? null;

                        if ($log) {
                            $log->out_time = $attendanceTime;
                        } else {
                            $log = new InOutLog;
                            $log->student_id = $student->id;
                            $log->in_time = $attendanceTime;
                        }

                        $log->save();

                        $this->sendNotification($student->student_id, 'تم تسجيل حضورك', 'تم تسجيل حضورك في الوقت '.$attendanceTime);
                    }
                }
            }

            ImportLog::create(['last_import_time' => now()]);
            $zk->disconnect();

            return response()->json(['success' => 'Attendance fetched and notifications sent successfully'], 200);
        } else {
            return response()->json(['error' => 'Failed to connect to the device'], 500);
        }
    }

    private function sendNotification($studentId, $title, $body)
    {
        $data = ['title' => $title, 'body' => $body];

        try {
            $FcmToken = Http::get('https://api.dev2.gomaplus.tech/api/getFcmTokensFromServer', [
                'student_id' => $studentId,
            ]);

            if ($FcmToken->successful()) {
                $firebaseNotification = new FirebaseService;
                $firebaseNotification->BasicSendNotification($title, $body, $FcmToken->json(), $data);
            } else {
                Log::error('Failed to retrieve FCM token for student_id: '.$studentId);
            }
        } catch (\Exception $e) {
            Log::error('Error sending notification: '.$e->getMessage());

            Notification::create([
                'student_id' => $studentId,
                'title' => $title,
                'body' => $body,
                'data' => json_encode($data),
            ]);
        }
    }

    public function getAttendance($studentID)
    {
        $attendance = InOutLog::where('student_id', $studentID)->get();

        return AttendanceResource::collection($attendance);
    }

    /// API For Flutter To Get Attendances
    public function getAttendances()
    {
        $studentID = auth::guard('api_student')->user()->id;

        $attendance = InOutLog::where('student_id', $studentID)
            ->orderBy('created_at', 'desc')
            ->get();

        return AttendanceResource::collection($attendance);
    }
}
