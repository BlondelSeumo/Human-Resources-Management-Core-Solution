<?php


namespace App\Mail\Tag;



use App\Http\Composer\Helper\AttendancePermissions;

class AttendanceTag extends Tag
{
    protected $user;

    protected $details;

    public function __construct($user, $notifier = null, $receiver = null, $details = null)
    {
        $this->user = $user;
        $this->notifier = $notifier;
        $this->receiver = $receiver;
        $this->details = $details;
        $this->resourceurl = AttendancePermissions::new(true)
                ->parseNotificationUrl($this->user->id, optional($this->details->status)->name).'&details_id='.$this->details->id.'&id='.$this->details->attendance_id;
    }

    function notification()
    {
        return array_merge([
            '{name}' => optional($this->user)->full_name,
        ], $this->common());
    }
}