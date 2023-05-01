<?php


namespace App\Mail\Tag;


use App\Http\Composer\Helper\AdministrationPermissions;

class WorkingShiftTag extends Tag
{
    protected $workingShift;

    public function __construct($workingShift, $notifier, $receiver)
    {
        $this->workingShift = $workingShift;
        $this->notifier = $notifier;
        $this->receiver = $receiver;
        $this->resourceurl = AdministrationPermissions::new(true)->workShiftUrl();
    }

    function notification()
    {
        return array_merge([
            '{name}' => $this->workingShift->name,
        ], $this->common());
    }
}
