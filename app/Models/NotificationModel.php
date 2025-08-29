<?php

namespace App\Models;

use Illuminate\Notifications\DatabaseNotification;
use Spatie\Translatable\HasTranslations;

class NotificationModel extends DatabaseNotification
{
    use HasTranslations;

    protected $table = 'notifications';

    protected $casts = [
        'data' => 'array',
        'category' => 'array',
    ];
}
