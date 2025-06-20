<?php

namespace App\Listeners;

use App\Events\ProfileUpdated;
use Illuminate\Support\Facades\Log;

class LogProfileUpdate
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ProfileUpdated $event)
    {
        Log::info('Profile updated', [
            'user_id' => $event->userId,
            'updated_fields' => $event->updatedFields,
        ]);
    }
}
