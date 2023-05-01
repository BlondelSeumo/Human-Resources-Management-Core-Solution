<?php

namespace App\Http\Controllers\Core\Notification;

use App\Filters\Core\BaseFilter;
use App\Http\Controllers\Controller;
use App\Models\Core\Setting\NotificationChannel;

class NotificationChannelController extends Controller
{
    public function __construct(BaseFilter $filter)
    {
        $this->filter = $filter;
    }

    /**
     * @return mixed
     */
    public function index()
    {
        return NotificationChannel::query()
            ->filters($this->filter)
            ->get();
    }
}
