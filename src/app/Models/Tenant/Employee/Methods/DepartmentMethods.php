<?php


namespace App\Models\Tenant\Employee\Methods;


trait DepartmentMethods
{
    public function isInActive()
    {
        return optional($this->status)->name === 'status_inactive';
    }

    public function isActive()
    {
        return optional($this->status)->name === 'status_active';
    }
}
