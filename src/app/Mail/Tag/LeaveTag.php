<?php


namespace App\Mail\Tag;


use App\Http\Composer\Helper\LeavePermissions;

class LeaveTag extends Tag
{
    protected $user;

    protected $leave;

    public function __construct($user, $notifier = null, $receiver = null, $leave = null)
    {
        $this->user = $user;
        $this->notifier = $notifier;
        $this->receiver = $receiver;
        $this->leave = $leave;
        $this->resourceurl = LeavePermissions::new(true)
                ->parseNotificationUrl($this->user->id, optional($this->leave->status)->name).'&details_id='.$this->leave->id;
    }

    function notification()
    {
        return array_merge([
            '{name}' => optional($this->user)->full_name,
        ], $this->common());
    }
}