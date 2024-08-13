<?php

namespace App\Http\Controllers;

use App\Models\ImportLog;
use App\Models\InOutLog;
use App\Models\Student;
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
                    }
                }
            }
            ImportLog::create(['last_import_time' => now()]);

            $zk->disconnect();
        } else {
            return response()->json(['error' => 'Failed to connect to the device'], 500);
        }

        return response()->json(['success' => 'Attendance fetched successfully'], 200);
    }

    public function test()
    {
        $zk = new ZKTeco('192.168.1.201');
        if ($zk->connect()) {
//         return $zk->getUser();
            return $zk->getAttendance();
            return response('OK', 200);
        }else
            return response('Connection error', 500);
    }

}
