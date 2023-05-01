<?php

namespace App\Services\Tenant\Import;

use App\Mail\Tenant\EmployeePasswordResetMail;
use App\Models\Core\Auth\User;
use App\Models\Core\Status;
use App\Models\Tenant\Employee\EmploymentStatus;
use App\Services\Core\Auth\UserInvitationService;
use App\Services\Tenant\Employee\EmployeeService;
use App\Services\Tenant\TenantService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class EmployeeImportService extends TenantService
{
    protected EmployeeService $employeeService;

    public function __construct(User $user, EmployeeService $employeeService)
    {
        $this->model = $user;
        $this->employeeService = $employeeService;
    }

    public function saveEmployee(): EmployeeImportService
    {
        $this->employeeService
            ->setModel($this->createUser())
            ->setAttributes($this->getAttributes())
            ->saveEmployeeId()
            ->saveJoiningDate()
            ->saveSalary()
            ->setIsInvite(true)
            ->assignToDepartment()
            ->assignToDesignation()
            ->assignEmploymentStatus();

        return $this;
    }

    public function createUser(): User
    {
        $status = Status::findByNameAndType('status_active')->id;

//        $invitation_token = base64_encode($this->getAttr('email').'-invitation-from-us');

        $this->model->fill([
            'first_name' => $this->getAttr('first_name'),
            'last_name' => $this->getAttr('last_name'),
            'email' => $this->getAttr('email'),
            'status_id' => $status,
            'invitation_token' => null,
            'is_in_employee' => 1,
        ])->save();

        resolve(UserInvitationService::class)
            ->setModel($this->model)
            ->assignRoles($this->getAttr('roles'));

        return $this->model;
    }

    public function sendPasswordResetMail(): EmployeeImportService
    {
        $terminated_employment_status_id = Cache::remember('terminated_employment_status_id', 300, function () {
            return EmploymentStatus::query()->where('alias', 'terminated')->first()->id;
        });
        if ($this->getAttr('employment_status_id') == $terminated_employment_status_id){
            return $this;
        }

        $token = base64_encode(microtime(true));
        DB::table('password_resets')->insert([
            'email' => $this->model->email,
            'token' => $token,
            'created_at' => nowFromApp()
        ]);

        try {
            Mail::to($this->model)
                ->send(new EmployeePasswordResetMail($this->model, $token));
        } catch (\Exception $exception) { /* Ignore */
        }
        return $this;
    }
}
