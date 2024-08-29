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
        $directoryPath = public_path('students_image');
        $studentsWithImages = Student::whereNotNull('image')
            ->where('is_image_synced', 0)
            ->get();

        foreach ($studentsWithImages as $student) {
            $imagePath = public_path($student->image);

            if (file_exists($imagePath)) {
                $response = Http::attach(
                    'image', file_get_contents($imagePath), basename($imagePath)
                )->post('https://api.dev2.gomaplus.tech/api/uploadImage', [
                    'student_id' => $student->id,
                ]);

                if ($response->successful()) {
                    $student->update(['is_image_synced' => true]);
                    $this->info('Image for student ID '.$student->id.' synced successfully.');
                } else {
                    $this->error('Failed to sync image for student ID '.$student->id.' - Status Code: '.$response->status());
                }
            } else {
                $this->error('Image file not found for student ID '.$student->id);
            }
        }
    }
}
