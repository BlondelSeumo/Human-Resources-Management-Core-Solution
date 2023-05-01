<?php

use App\Helpers\Core\General\NotificationHelper;
use App\Models\Core\Auth\User;

if (!function_exists('notify')) {

    function notify($users = null) {
            if (!$users)
                return new NotificationHelper();

            if ($users instanceof User)
                return (new NotificationHelper())->audiences($users->id);

            if (is_array($users))
                return (new NotificationHelper())->audiences($users);

            return new NotificationHelper();


    }

}
