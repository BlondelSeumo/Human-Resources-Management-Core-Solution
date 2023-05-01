<?php


namespace App\Services\Tenant\Payroll;


use App\Exceptions\GeneralException;
use App\Models\Core\Auth\User;
use App\Models\Tenant\Employee\Department;
use App\Models\Tenant\Payroll\Payrun;
use App\Repositories\Core\Status\StatusRepository;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ManualPayrollService extends PayrunService
{
    private array $ranges;
    private array $users;
    private object $beneficiaries;
    private Payrun $payrun;

    public function validations(): self
    {
        $this->generalValidation()
            ->payrunPeriodValidation()
            ->beneficiariesValidation();

        return $this;
    }

    public function generalValidation(): self
    {
        validator($this->getAttributes(), [
            'consider_type' => 'required',
            'payrun_period' => 'required',
            'departments' => "required_without:users|array",
            'users' => "required_without:departments|array",
        ],[
            'departments.required_without' => "Please select employees from by users or by departments",
            'users.required_without' => "Please select employees from by users or by departments",
        ])->validate();

        if (!count($this->getAttr('users') ?: [])){
            validator($this->getAttributes(), [
                'departments' => "min:1",
            ])->validate();
        }

        if (!count($this->getAttr('departments') ?: [])){
            validator($this->getAttributes(), [
                'users' => "min:1",
            ])->validate();
        }
        return $this;
    }

    public function payrunPeriodValidation(): self
    {
        if ($this->getAttr('payrun_period') == 'monthly') {
            validator($this->getAttributes(), [
                'executable_month' => 'required',
                'executable_year' => 'required',
            ])->validate();
        }else{
            validator($this->getAttributes(), [
                'end_date' => 'required',
                'start_date' => 'required',
            ])->validate();
        }

        return $this;
    }

    public function beneficiariesValidation(): self
    {
        validator($this->getAttributes(), [
            'allowancePercentages' => 'array|min:'.count($this->getAttr('allowances')),
            'allowanceValues' => 'array|min:'.count($this->getAttr('allowances')),
            'deductionPercentages' => 'array|min:'.count($this->getAttr('deductions')),
            'deductionValues' => 'array|min:'.count($this->getAttr('deductions')),
            'deductionValues.*' => 'required',
            'allowanceValues.*' => 'required',
        ],[
            'allowanceValues.min' => 'All allowance value field is required',
            'deductionValues.min' => 'All deduction value field is required',
            'allowanceValues.*.required' => 'All allowance value field is required',
            'deductionValues.*.required' => 'All deduction value field is required',
        ])->validate();

        return $this;
    }

    public function saveAndSetManualPayrun(): self
    {
        $attributes = [
            'name' => 'payrun_'.nowFromApp(),
            'status_id' => resolve(StatusRepository::class)->payrunGenerated(),
            'data' => json_encode(array_merge([
                'time_range' => $this->ranges,
                'employees' => $this->users,
                'type' => 'manual',
                'users' => $this->getAttr('users') ?: [],
                'departments' => $this->getAttr('departments') ?: [],
                'consider_type' => $this->getAttr('consider_type'),
                'period' => $this->getAttr('payrun_period'),
                'consider_overtime' => $this->getAttr('consider_overtime'),
            ], $this->getCommonPayslipSettingData())),
            'followed' => 'customized',
        ];

        $this->setPayrun($this->savePayrun($attributes));

        return $this;
    }

    public function saveAndSetBeneficiaries(): self
    {
        $beneficiariesAttr = [];

        foreach ($this->getAttr('allowances') as $key => $allowance){
            array_push($beneficiariesAttr, [
                'beneficiary_id' => $allowance,
                'amount' => $this->getAttr('allowanceValues')[$key],
                'is_percentage' => $this->getAttr('allowancePercentages')[$key]
            ]);
        }

        foreach ($this->getAttr('deductions') as $key => $deduction){
            array_push($beneficiariesAttr, [
                'beneficiary_id' => $deduction,
                'amount' => $this->getAttr('deductionValues')[$key],
                'is_percentage' => $this->getAttr('deductionPercentages')[$key]
            ]);
        }

        $this->payrun->beneficiaries()->createMany($beneficiariesAttr);

        $this->setBeneficiaries($this->payrun->load(['beneficiaries', 'beneficiaries.beneficiary:id,type'])->beneficiaries);

        return $this;
    }

    public function generateManualPayrunPayslips(): self
    {
        $settings = [
            'consider_type' => $this->getAttr('consider_type'),
            'period' => $this->getAttr('payrun_period'),
            'consider_overtime' => $this->getAttr('consider_overtime'),
        ];

        $users = User::query()->whereIn('id', $this->users)->get();

        $this->employeePayrunPayslipGenerated($this->payrun, $users, $settings, $this->beneficiaries->load('beneficiary')->toArray(), $this->ranges, true);

        return $this;
    }

    public function setRanges($ranges = []): self
    {
        $this->ranges = count($ranges) ? $ranges : $this->makeRangesByPeriodType();

        return $this;
    }

    public function makeRangesByPeriodType(): array
    {
        if (!$this->getAttr('payrun_period')){
            return [];
        }

        if ($this->getAttr('payrun_period') == 'monthly'){
            return $this->getDateRangesByMonthYear($this->getAttr('executable_month'), $this->getAttr('executable_year'));
        }

        return [
            $this->carbon($this->getAttr('start_date'))->parse()->toDateString(),
            $this->carbon($this->getAttr('end_date'))->parse()->toDateString()
        ];
    }

    public function setUsers(): self
    {
        $departmentUser = [];
        $statusActive = resolve(StatusRepository::class)->userActive();

        if (count($this->getAttr('departments') ?? [])) {
            $departmentUser = Department::query()
                ->whereIn('id', $this->getAttr('departments'))
                ->with([
                    'users' => fn (BelongsToMany $builder) => $builder
                        ->where('status_id', $statusActive)
                        ->select('id')
                ])->get()
                ->pluck('users')
                ->flatten()
                ->pluck('id')
                ->toArray();
        }

        $this->users = array_unique(array_merge($departmentUser, $this->getAttr('users') ?? [])) ?: [];

        throw_if(
            !count($this->users),
            new GeneralException(__t('no_user_found'))
        );
        return $this;
    }

    public function setBeneficiaries($beneficiaries): self
    {
        $this->beneficiaries = $beneficiaries;

        return $this;
    }

    /**
     * @param Payrun $payrun
     */
    public function setPayrun(Payrun $payrun): self
    {
        $this->payrun = $payrun;

        return $this;
    }

    public function deleteAllUnderPayrun(): self
    {
        $this->payrun->beneficiaries()->delete();
        $this->payrun->payslips()->get()->map(function ($item){
            $item->beneficiaries()->delete();
        });
        $this->payrun->payslips()->delete();

        return $this;
    }

    public function validateForUpdate(Payrun $payrun): self
    {
        throw_if(
            $payrun->followed != 'customized' &&
            $payrun->status_id != resolve(StatusRepository::class)->payrunGenerated(),
            new GeneralException(__t('action_not_allowed'))
        );

        return $this;
    }

    public function updatePayrun(Payrun $payrun): self
    {
        $payrun->update([
            'data' => json_encode([
                'time_range' => $this->ranges,
                'employees' => $this->users,
                'type' => 'manual',
                'users' => $this->getAttr('users') ?: [],
                'departments' => $this->getAttr('departments') ?: [],
                'consider_type' => $this->getAttr('consider_type'),
                'period' => $this->getAttr('payrun_period'),
                'consider_overtime' => $this->getAttr('consider_overtime'),
                'note' => $this->getAttr('note')
            ]),
        ]);

        return $this;
    }

    public function dateRangeValidations(): self
    {
        throw_if(
            $this->carbon($this->ranges[0])->parse()->monthName != $this->carbon($this->ranges[1])->parse()->monthName,
            new GeneralException(__t('action_not_allowed'))
        );

        return $this;
    }
}