<?php

namespace App\Console\Commands;

use App\Models\Notification;
use App\Services\FirebaseService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class RetryFailedNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:retry-failed-notifications';

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
        $failedNotifications = Notification::all();
        $firebaseNotification = new FirebaseService;

        foreach ($failedNotifications as $notification) {
            try {
                $FcmToken = Http::get('https://api.dev2.gomaplus.tech/api/getFcmTokensFromServer', [
                    'student_id' => $notification->student_id,
                ]);

                $firebaseNotification->BasicSendNotification(
                    $notification->title,
                    $notification->body,
                    $FcmToken->json(),
                    json_decode($notification->data, true)
                );

                $notification->delete();
            } catch (\Exception $e) {
                $this->error('Failed to resend notification ID: '.$notification->id);
            }
        }

        $this->info('Retry process completed.');
    }
}
