<?php

namespace App\Notifications\Tenant;

use App\Http\Composer\Helper\EmployeePermissions;
use App\Mail\Tag\SalaryTag;
use App\Notifications\BaseNotification;
use App\Notifications\Tenant\Helper\CommonParser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class SalaryIncrementNotification extends BaseNotification implements ShouldQueue
{
    use Queueable;

    use CommonParser;

    public $updatedSalary;

    public function __construct($templates, $via, $user, $updatedSalary)
    {
        $this->templates = $templates;
        $this->via = $via;
        $this->model = $user;
        $this->auth = auth()->user();
        $this->tag =  SalaryTag::class;
        $this->updatedSalary = $updatedSalary;
        parent::__construct();
    }

    public function parseNotification()
    {
        $this->databaseNotificationUrl = EmployeePermissions::new(true)->profile($this->model) . '?tab=Salary Overview';

        $this->common();
    }

    public function tagParams($notifiable)
    {
        return [
            $this->model,
            $this->auth,
            $notifiable,
            $this->updatedSalary
        ];
    }
}

