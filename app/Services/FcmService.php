<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FcmService
{
    protected $messaging;

    public function __construct()
    {
        $firebase = (new Factory)
            ->withServiceAccount(base_path(env('FIREBASE_CREDENTIALS')));

        $this->messaging = $firebase->createMessaging();
    }

    /**
     * Send a push notification to a single device token
     */
    public function sendNotification(string $deviceToken, string $title, string $body, array $data = [])
    {
        $notification = Notification::create($title, $body);

        $message = CloudMessage::withTarget('token', $deviceToken)
            ->withNotification($notification)
            ->withData($data);

        return $this->messaging->send($message);
    }

    /**
     * Send a notification to multiple users
     */
    public function notifyUsers(iterable $users, string $title, string $body, array $data = [])
    {
        foreach ($users as $user) {
            if ($user->fcm_token) {
                $this->sendNotification($user->fcm_token, $title, $body, $data);
            }
        }
    }
}
