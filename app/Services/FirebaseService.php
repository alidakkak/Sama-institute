<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FirebaseService
{
    protected $messaging;

    public function __construct()
    {

        $firebase = (new Factory)
//            ->withServiceAccount(config('services.firebase.credentials'));
        ->withServiceAccount(config_path('firebase_credentials.json'));

        $this->messaging = $firebase->createMessaging();
    }

    public function BasicSendNotification($title, $body, $FcmToken, $data)
    {
        $notification = Notification::create($title, $body);
        foreach ($FcmToken as $token) {
            $message = CloudMessage::withTarget('token', $token)
                ->withNotification($notification)
                ->withData($data);
            try {
                $this->messaging->send($message);
            } catch (\Exception $e) {
                Log::error('Failed to send notification , request failed with message : '.$e->getMessage());
            }
        }
    }
}
