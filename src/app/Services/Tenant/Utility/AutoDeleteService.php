<?php


namespace App\Services\Tenant\Utility;


use App\Helpers\Core\Traits\InstanceCreator;
use App\Models\Tenant\Employee\Department;
use App\Models\Tenant\Employee\Designation;
use App\Models\Tenant\Employee\EmploymentStatus;
use App\Models\Tenant\Leave\LeavePeriod;
use App\Models\Tenant\Leave\LeaveType;
use App\Services\Tenant\TenantService;

class AutoDeleteService extends TenantService
{
    use InstanceCreator;
    public function trigger(int $tenant_id) : bool
    {
        if(class_exists(\App\Models\App\Tenant\Tenant::class)){
            \App\Models\App\Tenant\Tenant::findOrFail($tenant_id);
        }
        $this->deleteEmploymentStatus($tenant_id);
        $this->deleteLeavePeriod($tenant_id);
        $this->deleteLeaveCategory($tenant_id);
        $this->deleteDesignation($tenant_id);
        $this->deleteDepartment($tenant_id);

        return true;
    }
    public function deleteEmploymentStatus(int $tenant_id) : void
    {
        EmploymentStatus::whereTenantId($tenant_id)->delete();
    }
    public function deleteLeavePeriod(int $tenant_id) : void
    {
        LeavePeriod::whereTenantId($tenant_id)->delete();
    }
    public function deleteLeaveCategory(int $tenant_id) : void
    {
        LeaveType::whereTenantId($tenant_id)->delete();
    }
    public function deleteDesignation(int $tenant_id) : void
    {
        Designation::whereTenantId($tenant_id)->delete();
    }
    public function deleteDepartment(int $tenant_id) : void
    {
        Department::whereTenantId($tenant_id)->delete();
    }

}

