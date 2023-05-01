<?php


namespace App\Http\Controllers\Tenant\Employee;


use App\Exceptions\GeneralException;
use App\Hooks\User\AfterUserConfirmed;
use App\Hooks\User\AfterUserInvited;
use App\Hooks\User\BeforeUserInvited;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Employee\EmployeeRequest;
use App\Mail\Tenant\EmployeeInvitationCancelMail;
use App\Models\Core\Auth\Role;
use App\Models\Core\Auth\User;
use App\Notifications\Core\User\UserInvitationNotification;
use App\Services\Tenant\Employee\EmployeeInviteService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class EmployeeInviteController extends Controller
{
    protected AfterUserInvited $afterUserInvited;

    protected BeforeUserInvited $beforeUserInvited;

    public function __construct(EmployeeInviteService $service, BeforeUserInvited $beforeUserInvited, AfterUserInvited $afterUserInvited)
    {
        $this->service = $service;
        $this->afterUserInvited = $afterUserInvited;
        $this->beforeUserInvited = $beforeUserInvited;
    }

    public function invite(EmployeeRequest $request)
    {
        DB::transaction(function () use ($request) {
            $this->beforeUserInvited
                ->handle();

            $user = $this->service
                ->setAttributes($request->except('allowed_resource', 'tenant_id', 'tenant_short_name'))
                ->when(!auth()->user()->can('attach_users_to_roles'), function (EmployeeInviteService $service){
                    $employeeRoleId = Role::query()
                        ->where('alias', 'employee')
                        ->get()
                        ->pluck('id')
                        ->toArray();
                    $service->setAttr('roles', $employeeRoleId);
                })->validateRoles()
                ->validateMailSettings()
                ->invite();

            $this->afterUserInvited
                ->setModel($user)
                ->cacheQueueClear();

            notify()
                ->on('employee_invited')
                ->with($user)
                ->send(UserInvitationNotification::class);

            $this->afterUserInvited
                ->setModel($user)
                ->handle();
        });

        return response()->json([
            'status' => true,
            'message' => trans('default.invite_employee_response')
        ]);
    }

    public function create(EmployeeRequest $request)
    {
        DB::transaction(function () use ($request) {
            $this->beforeUserInvited
                ->handle();

            $user = $this->service
                ->setAttributes($request->except('allowed_resource', 'tenant_id', 'tenant_short_name'))
                ->when(!auth()->user()->can('attach_users_to_roles'), function (EmployeeInviteService $service){
                    $employeeRoleId = Role::query()
                        ->where('alias', 'employee')
                        ->get()
                        ->pluck('id')
                        ->toArray();
                    $service->setAttr('roles', $employeeRoleId);
                })->validateRoles()
                ->validateMailSettings()
                ->create();

            $this->afterUserInvited
                ->setModel($user)
                ->cacheQueueClear();

            AfterUserConfirmed::new()
                ->setModel($user)
                ->handle();
        });

        return response()->json([
            'status' => true,
            'message' => trans('default.create_employee_response')
        ]);
    }

    public function cancel(User $employee)
    {
        throw_if(
            !$employee->isInvited(),
            new GeneralException(__t('action_not_allowed'))
        );

        DB::transaction(function () use ($employee) {
             $this->service
                 ->setModel($employee)
                 ->cancel();

             Mail::to($employee->email)
                 ->send((new EmployeeInvitationCancelMail((object)$employee->toArray()))->delay(5));
        });

        return response()->json(['status' => true, 'message' => __t('employee_invitation_canceled_successfully')]);
    }
}