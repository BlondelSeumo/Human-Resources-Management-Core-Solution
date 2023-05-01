<?php


namespace App\Mail\Tag;


use App\Http\Composer\Helper\AdministrationPermissions;

class DepartmentTag extends Tag
{
    protected $department;

    public function __construct($department, $notifier, $receiver)
    {
        $this->department = $department;
        $this->notifier = $notifier;
        $this->receiver = $receiver;
        $this->resourceurl = AdministrationPermissions::new(true)->departmentUrl();
    }

    function notification()
    {
        return array_merge([
            '{name}' => $this->department->name,
        ], $this->common());
    }
}
