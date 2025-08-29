<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewBookNotification extends Notification
{
    use Queueable;

    protected $book;

    public function __construct($book)
    {
        $this->book = $book;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => [
                'en' => $this->book->getTranslation('title', 'en'),
                'ar' => $this->book->getTranslation('title', 'ar'),
            ],
            'message' => [
                'en' => "New book '{$this->book->getTranslation('title', 'en')}' was added in category {$this->book->category->getTranslation('name', 'en')}.",
                'ar' => "تمت إضافة كتاب جديد '{$this->book->getTranslation('title', 'ar')}' في فئة {$this->book->category->getTranslation('name', 'ar')}."
            ],
            'type' => 'info',
        ];
    }
}
