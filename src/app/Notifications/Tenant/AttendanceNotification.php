<?php

namespace App\Notifications\Tenant;

use App\Http\Composer\Helper\AttendancePermissions;
use App\Http\Composer\Helper\EmployeePermissions;
use App\Mail\Tag\AttendanceTag;
use App\Models\Core\Auth\User;
use App\Models\Tenant\Attendance\AttendanceDetails;
use App\Notifications\BaseNotification;
use App\Notifications\Tenant\Helper\CommonParser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class AttendanceNotification extends BaseNotification implements ShouldQueue
{
    use Queueable;

    use CommonParser;

    public AttendanceDetails $details;

    public function __construct(
        $templates,
        $via,
        AttendanceDetails $details,
        User $user
    )
    {
        $this->templates = $templates;
        $this->via = $via;
        $this->model = $user;
        $this->auth = auth()->user();
        $this->details = $details;
        $this->tag = AttendanceTag::class;
        parent::__construct();
    }


    public function parseNotification()
    {
        $this->mailView = 'notification.mail.template';

        $this->databaseNotificationUrl = AttendancePermissions::new(true)
                ->parseNotificationUrl($this->model->id, optional($this->details->status)->name). '&details_id=' . $this->details->id . '&id=' . $this->details->attendance_id;

        $this->common();
    }

    public function tagParams($notifiable)
    {
        return [
            $this->model,
            $this->auth,
            $notifiable,
            $this->details
        ];
    }
}
