<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphTo;

class Notification extends \Illuminate\Notifications\DatabaseNotification
{
    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }
}