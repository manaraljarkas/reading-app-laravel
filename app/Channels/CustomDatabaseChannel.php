<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;

class CustomDatabaseChannel
{
    public function send($notifiable, Notification $notification)
    {
        $data = $notification->toDatabase($notifiable);

        // Separate out category if present
        $category = $data['category'] ?? null;
        unset($data['category']);

        return $notifiable->routeNotificationFor('database')->create([
            'id'              => $notification->id,
            'type'            => get_class($notification),
            'notifiable_id'   => $notifiable->getKey(),
            'notifiable_type' => get_class($notifiable),
            'data'            => $data,
            'category'        => $category, // 👈 stored in its own column
            'read_at'         => null,
        ]);
    }
}
