<?php

namespace App\Http\Controllers;

use App\Http\Resources\AttendanceResource;
use App\Models\DeviceToken;
use App\Models\ImportLog;
use App\Models\InOutLog;
use App\Models\Student;
use App\Services\FirebaseService;
use Jmrashed\Zkteco\Lib\ZKTeco;
use Ramsey\Uuid\Type\Integer;

class AttendanceController extends Controller
{
    public function fetchAttendance()
    {
        $zk = new ZKTeco('192.168.1.201');

        if ($zk->connect()) {
            $attendances = $zk->getAttendance();

            // الحصول على آخر وقت استيراد من قاعدة البيانات
            $lastImport = ImportLog::orderBy('last_import_time', 'desc')->first();
            $lastImportTime = $lastImport ? $lastImport->last_import_time : null;

            foreach ($attendances as $attendance) {
                $attendanceTime = date('Y-m-d H:i:s', strtotime($attendance['timestamp']));

                // استيراد فقط السجلات التي تمت بعد آخر وقت استيراد
                if ($lastImportTime === null || $attendanceTime > $lastImportTime) {
                    $student = Student::where('device_user_id', $attendance['uid'])->first();

                    if ($student) {
                        $log = InOutLog::where('student_id', $student->id)
                            ->whereDate('in_time', date('Y-m-d', strtotime($attendance['timestamp'])))
                            ->first();

                        if ($log) {
                            $log->out_time = $attendanceTime;
                        } else {
                            $log = new InOutLog();
                            $log->student_id = $student->id;
                            $log->in_time = $attendanceTime;
                        }

                        $log->save();

                        // إرسال إشعار للطالب عند تسجيل الدخول أو الخروج
                        $title = 'تم تسجيل حضورك';
                        $body = 'تم تسجيل حضورك في الوقت ' . $attendanceTime;
                        $FcmToken = DeviceToken::where('student_id', $student->id)->pluck('device_token')->toArray();

                        $data = ['title' => $title, 'body' => $body];
                        $firebaseNotification = new FirebaseService;
                        $firebaseNotification->BasicSendNotification($title, $body, $FcmToken, $data);
                    }
                }
            }

            // تحديث وقت الاستيراد الأخير
            ImportLog::create(['last_import_time' => now()]);

            // قطع الاتصال بجهاز البصمة
            $zk->disconnect();

            return response()->json(['success' => 'Attendance fetched and notifications sent successfully'], 200);
        } else {
            return response()->json(['error' => 'Failed to connect to the device'], 500);
        }
    }


    public function test()
    {
        $student = Student::find(6); // على سبيل المثال، استخدام ID معين للطالب

        // إذا كان الطالب موجودًا
        if ($student) {
            $title = 'تم تسجيل حضورك';
            $attendanceTime = now()->format('Y-m-d H:i:s'); // يمكنك تخصيص الوقت كما تشاء
            $body = 'تم تسجيل حضورك في الوقت ' . $attendanceTime;
            $FcmToken = DeviceToken::where('student_id', $student->id)->pluck('device_token')->toArray();

            $data = ['title' => $title, 'body' => $body];
            $firebaseNotification = new FirebaseService;
            $firebaseNotification->BasicSendNotification($title, $body, $FcmToken, $data);
        } else {
            return response()->json(['error' => 'Student not found'], 404);
        }
    }

    public function getAttendance($studentID)
    {
        $attendance = InOutLog::where('student_id', $studentID)->get();
        return AttendanceResource::collection($attendance);
    }

}
