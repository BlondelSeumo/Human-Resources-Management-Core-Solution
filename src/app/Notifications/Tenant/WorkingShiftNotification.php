<?php

namespace App\Notifications\Tenant;

use App\Http\Composer\Helper\AdministrationPermissions;
use App\Mail\Tag\WorkingShiftTag;
use App\Notifications\BaseNotification;
use App\Notifications\Tenant\Helper\CommonParser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class WorkingShiftNotification extends BaseNotification implements ShouldQueue
{
    use Queueable, CommonParser;

    public function __construct($templates, $via, $working_shift)
    {
        $this->templates = $templates;
        $this->via = $via;
        $this->model = $working_shift;
        $this->auth = auth()->user();
        $this->tag = WorkingShiftTag::class;
        parent::__construct();
    }

    public function parseNotification()
    {
        $this->databaseNotificationUrl = AdministrationPermissions::new(true)
            ->workShiftUrl();

        $this->common();
    }
}
