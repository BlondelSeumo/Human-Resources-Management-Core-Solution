<?php

namespace App\Services\Tenant\Employee;

use App\Models\Tenant\Employee\Designation;
use App\Models\Tenant\Employee\DesignationUser;
use App\Services\Tenant\TenantService;

class DesignationService extends TenantService
{
    protected $designationId;

    public function __construct(Designation $designation )
    {
        $this->model = $designation;
    }

    public function assignToUsers($users)
    {
        $users = is_array($users) ? $users : func_get_args();

        $this->endPreviousDesignationOfUsers($users);

        DesignationUser::insert(
            array_map(
                fn($user) => [
                    'user_id' => $user,
                    'start_date' => todayFromApp()->format('Y-m-d'),
                    'designation_id' => $this->getDesignationId()
                ],
                DesignationUser::getNoneExistedUsers($this->getDesignationId(), $users)
            )
        );
    }

    public function endPreviousDesignationOfUsers($users = [])
    {
        $users = is_array($users) ? $users : func_get_args();

        DesignationUser::whereIn('user_id', $users)
            ->whereNull('end_date')
            ->where('designation_id', '!=', $this->getDesignationId())
            ->update([
                'end_date' => todayFromApp()->format('Y-m-d')
            ]);

        return $this;
    }

    public function setDesignationId($designationId): DesignationService
    {
        $this->designationId = $designationId;
        return $this;
    }


    public function getDesignationId()
    {
        return $this->designationId ?: $this->model->id;
    }

}
