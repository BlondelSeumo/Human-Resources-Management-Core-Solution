<?php


namespace App\Hooks\User;


use App\Hooks\HookContract;
use App\Services\Tenant\Employee\EmployeeService;
use Illuminate\Support\Facades\Artisan;

class AfterUserInvited extends HookContract
{

    public function handle()
    {
//        resolve(EmployeeService::class)
//            ->setModel($this->model)
//            ->setAttributes(request()->except('allowed_resource', 'tenant_id', 'tenant_short_name'))
//            ->saveEmployeeId()
//            ->assignToDepartment()
//            ->assignToDesignation()
//            ->assignEmploymentStatus();
        return $this->model;
    }

    public function cacheQueueClear(): self
    {
        Artisan::call('config:clear');
        return $this;
    }
}
