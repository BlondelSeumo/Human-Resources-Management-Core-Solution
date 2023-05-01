<?php


namespace App\Helpers\Notification;


class NotificationEventPicker
{
    public function on($event)
    {
        return ['audiences' => collect([]), 'templates' => collect([]), 'via' => []];
    }
}
