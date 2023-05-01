<?php

namespace App\Notifications\Tenant;

use App\Http\Composer\Helper\LeavePermissions;
use App\Mail\Tag\AttendanceTag;
use App\Mail\Tag\LeaveTag;
use App\Models\Core\Auth\User;
use App\Models\Tenant\Leave\Leave;
use App\Notifications\BaseNotification;
use App\Notifications\Tenant\Helper\CommonParser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class LeaveNotification extends BaseNotification implements ShouldQueue
{
    use Queueable;

    use CommonParser;

    private Leave $leave;

    public function __construct(
        $templates,
        $via,
        Leave $leave,
        User $user)
    {
        $this->templates = $templates;
        $this->via = $via;
        $this->model = $user;
        $this->auth = auth()->user();
        $this->leave = $leave;
        $this->tag = LeaveTag::class;
        parent::__construct();
    }
    public function parseNotification()
    {
        $this->mailView = 'notification.mail.template';

        $this->leave->load('status');
        $this->databaseNotificationUrl = LeavePermissions::new(true)
                ->parseNotificationUrl($this->model->id, optional($this->leave->status)->name). '&leave_id=' . $this->leave->id;

        $this->common();
    }

    public function tagParams($notifiable)
    {
        return [
            $this->model,
            $this->auth,
            $notifiable,
            $this->leave
        ];
    }
}
