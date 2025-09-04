<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FcmService
{
    protected $messaging = null;

    protected function getMessaging()
    {
        if ($this->messaging === null) {
            $credentials = env('FIREBASE_CREDENTIALS');

            if (!$credentials || !file_exists(base_path($credentials))) {
                return null;
            }

            $firebase = (new Factory)
                ->withServiceAccount(base_path($credentials));

            $this->messaging = $firebase->createMessaging();
        }

        return $this->messaging;
    }

    public function sendNotification(string $deviceToken, string $title, string $body, array $data = [])
    {
        $messaging = $this->getMessaging();
        if (!$messaging) {
            Log::warning('Firebase not configured, skipping notification.');
            return false;
        }

        $notification = Notification::create($title, $body);

        $message = CloudMessage::withTarget('token', $deviceToken)
            ->withNotification($notification)
            ->withData($data);

        return $messaging->send($message);
    }

    public function notifyUsers(iterable $users, string $title, string $body, array $data = [])
    {
        foreach ($users as $user) {
            if ($user->fcm_token) {
                $this->sendNotification($user->fcm_token, $title, $body, $data);
            }
        }
    }
}
