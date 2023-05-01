<?php

namespace App\Http\Controllers\Tenant\Employee;

use App\Helpers\Traits\SettingHelper;
use App\Helpers\Traits\TenantAble;
use App\Http\Controllers\Controller;
use App\Models\Core\Auth\User;
use App\Models\Tenant\Payroll\PayrunSetting;
use App\Repositories\Core\Setting\SettingRepository;
use App\Services\Tenant\Payroll\EmployeePayrunService;
use App\Services\Tenant\Payroll\PayslipService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class EmployeePayrunController extends Controller
{
    use TenantAble, SettingHelper;

    public function __construct(EmployeePayrunService $service, SettingRepository $repository)
    {
        $this->service = $service;
        $this->repository = $repository;
    }

    public function index(User $employee)
    {


        [$setting_able_id, $setting_able_type] = $this->tenantAble();
        $default_payrun = resolve(SettingRepository::class)->getFormattedSettings(
            'payrun', $setting_able_type, $setting_able_id
        );
        $default_beneficiary = resolve(SettingRepository::class)->getFormattedSettings(
            'beneficiary', $setting_able_type, $setting_able_id
        );
        $this->service
            ->setModel($employee)
            ->validateForEmployeesPayslipsAccess(\request()->get('show_all'))
            ->validateUserException($default_payrun, 'payrun_restriction')
            ->validateUserException($default_beneficiary, 'beneficiary_restriction')
            ->setDefaultSettingRelation($default_payrun);

        return $employee->load([
            'payrunSetting',
            'payrunBeneficiaries',
            'payrunBeneficiaries.beneficiary',
        ])->unsetRelation('department');

    }

    public function updatePayrun(User $employee)
    {
        validator(request()->all(), [
            'payrun_period' => ['required'],
            'consider_type' => ['required'],
        ])->validate();

        PayrunSetting::query()->updateOrCreate(
            [
                'payrun_settingable_type' => User::class,
                'payrun_settingable_id' => $employee->id
            ],
            [
                'payrun_period' => \request()->get('payrun_period'),
                'consider_type' => \request()->get('consider_type'),
                'consider_overtime' => \request()->get('consider_overtime')
            ]
        );

        return updated_responses('payrun_setting');
    }

    public function updateBeneficiary(User $employee)
    {
        resolve(PayslipService::class)
            ->setModel($employee)
            ->setAttributes(\request()->only([
                'allowances',
                'allowanceValues',
                'allowancePercentages',
                'deductions',
                'deductionValues',
                'deductionPercentages',
            ]))
            ->beneficiariesValidation()
            ->updateBeneficiaries('payrunBeneficiaries');

        return updated_responses('beneficiary_badge');
    }

    public function restore(User $employee)
    {
        if (\request('type') == 'payrun-period') {
            $employee->payrunSetting()->delete();
        } else if (\request('type') == 'beneficiaries') {
            $employee->payrunBeneficiaries()->delete();
        }

        return updated_responses('payrun_setting');
    }
}
