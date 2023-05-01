<?php

namespace App\Notifications\Tenant;

use App\Http\Composer\Helper\EmployeePermissions;
use App\Mail\Tag\EmployeeTag;
use App\Notifications\BaseNotification;
use App\Notifications\Tenant\Helper\CommonParser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmployeeTerminateNotification extends BaseNotification implements ShouldQueue
{
    use Queueable;

    use CommonParser;

    public function __construct($templates, $via, $user)
    {
        $this->templates = $templates;
        $this->via = $via;
        $this->model = $user;
        $this->auth = auth()->user();
        $this->tag = EmployeeTag::class;
        parent::__construct();
    }

    public function parseNotification()
    {

        $this->databaseNotificationUrl = EmployeePermissions::new(true)->profile($this->model);

        $this->common();
    }
}
