<?php

namespace App\Console\Commands;

use App\Models\ImportLog;
use App\Models\InOutLog;
use App\Models\Notification;
use App\Models\Student;
use App\Services\FirebaseService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Jmrashed\Zkteco\Lib\ZKTeco;

class FetchAttendance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-attendance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $zk = new ZKTeco('192.168.1.201');

        if ($zk->connect()) {
            $this->info('Connected to the ZKTeco device successfully.');

            $attendances = $zk->getAttendance();
            if (empty($attendances)) {
                $this->warn('No attendance records found.');

                return;
            }

            $lastImport = ImportLog::orderBy('last_import_time', 'desc')->first();
            $lastImportTime = $lastImport ? $lastImport->last_import_time : null;

            $uids = array_column($attendances, 'id');
            $students = Student::whereIn('device_user_id', $uids)->get()->keyBy('device_user_id');
            $logs = InOutLog::whereIn('student_id', $students->pluck('id'))
//                ->whereDate('in_time', now()->toDateString())
                ->get()
                ->keyBy('student_id');

            foreach ($attendances as $attendance) {
                $attendanceTime = date('Y-m-d H:i:s', strtotime($attendance['timestamp']));

                if ($lastImportTime === null || $attendanceTime > $lastImportTime) {
                    $student = $students[$attendance['id']] ?? null;

                    if ($student) {
                        $log = $logs[$student->id] ?? null;

                        if ($log && $log->out_time === null) {
                            // إذا كان هناك سجل موجود ولكن لم يتم تسجيل الخروج بعد، نقوم بتحديث وقت الخروج
                            $log->out_time = $attendanceTime;
                            $log->save();
                        } else {
                            // إذا كان لا يوجد سجل أو تم تسجيل الخروج بالفعل، نقوم بإنشاء سجل جديد للدخول
                            $log = new InOutLog;
                            $log->student_id = $student->id;
                            $log->in_time = $attendanceTime;
                            $log->save();
                        }

                    } else {
                        $this->warn("Student not found for UID: {$attendance['id']}");
                    }
                }
            }

            ImportLog::create(['last_import_time' => now()]);

            $zk->disconnect();

            $this->info('Attendance fetched and saved successfully.');
        } else {
            $this->error('Failed to connect to the ZKTeco device.');
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
}
