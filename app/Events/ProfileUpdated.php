<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProfileUpdated
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     */
    public $userId;
    public $updatedFields;

    public function __construct($userId, array $updatedFields)
    {
        $this->userId = $userId;
        $this->updatedFields = $updatedFields;
    }
}
