<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BadgeUnlockedNotification extends Notification
{
    use Queueable;

    protected $badge;

    public function __construct($badge)
    {
        $this->badge = $badge;
    }

    public function via($notifiable)
    {
        return [\App\Channels\CustomDatabaseChannel::class];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => [
                'en' => $this->badge->getTranslation('title', 'en'),
                'ar' => $this->badge->getTranslation('title', 'ar'),
            ],
            'message' => [
                'en' => "You've unlocked the badge: "
                    . $this->badge->getTranslation('title', 'en')
                    . ". " . $this->badge->getTranslation('achievment', 'en'),
                'ar' => "لقد حصلت على شارة: "
                    . $this->badge->getTranslation('title', 'ar')
                    . ". " . $this->badge->getTranslation('achievment', 'ar'),
            ],
            'category' => json_encode([
                'en' => "Badge awarded",
                'ar' => "الحصول على شارة",
            ]),
        ];
    }
}
