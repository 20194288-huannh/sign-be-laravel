<?php

namespace App\Services;

use App\Models\Notification;

class NotificationService
{
    public function updateOrCreate($notification)
    {
        return Notification::updateOrCreate($notification, []);
    }
}
