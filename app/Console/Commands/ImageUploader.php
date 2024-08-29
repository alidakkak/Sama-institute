<?php

namespace App\Console\Commands;

use App\Models\Student;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ImageUploader extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:image-uploader';

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
        // جلب الطلاب الذين لديهم صور غير مرفوعة بعد
        $studentsWithImages = Student::whereNotNull('image')
            ->where('is_image_synced', 0)
            ->get()
            ->filter(function ($student) {
                return strpos($student->image, '/students_image/') === 0;
            });

        $results = [];

        foreach ($studentsWithImages as $student) {
            $imagePath = public_path($student->image);

            if (file_exists($imagePath)) {
                $response = Http::attach(
                    'image', file_get_contents($imagePath), basename($imagePath)
                )->post('https://api.dev2.gomaplus.tech/api/uploadImage', [
                    'student_id' => $student->id,
                ]);

                if ($response->successful()) {
                    $student->update(['is_image_synced' => 1]);
                    $results[] = ['student_id' => $student->id, 'status' => 'synced'];
                } else {
                    $results[] = [
                        'student_id' => $student->id,
                        'status' => 'failed',
                        'error' => 'Status Code: '.$response->status()
                    ];
                }
            } else {
                $results[] = [
                    'student_id' => $student->id,
                    'status' => 'failed',
                    'error' => 'Image file not found'
                ];
            }
        }

        if (empty($results)) {
            return response()->json(['message' => 'No students with images to sync.'], 200);
        }

        return response()->json($results, 200);
    }

}
