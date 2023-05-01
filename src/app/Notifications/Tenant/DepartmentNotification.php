<?php

namespace App\Notifications\Tenant;

use App\Http\Composer\Helper\AdministrationPermissions;
use App\Mail\Tag\DepartmentTag;
use App\Notifications\BaseNotification;
use App\Notifications\Tenant\Helper\CommonParser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class DepartmentNotification extends BaseNotification implements ShouldQueue
{
    use Queueable, CommonParser;

    public function __construct($templates, $via, $department)
    {
        $this->templates = $templates;
        $this->via = $via;
        $this->model = $department;
        $this->auth = auth()->user();
        $this->tag = DepartmentTag::class;
        parent::__construct();
    }


    public function parseNotification()
    {
        $this->databaseNotificationUrl = AdministrationPermissions::new(true)
            ->departmentUrl();

        $this->common();
    }
}
