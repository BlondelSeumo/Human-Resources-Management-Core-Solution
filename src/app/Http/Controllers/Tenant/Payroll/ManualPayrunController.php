<?php

namespace App\Http\Controllers\Tenant\Payroll;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Payroll\Payrun;
use App\Services\Tenant\Payroll\ManualPayrollService;
use Illuminate\Support\Facades\DB;

class ManualPayrunController extends Controller
{

    public function __construct(ManualPayrollService $service)
    {
        $this->service = $service;
    }

    public function index(Payrun $payrun)
    {
        return $payrun->load([
            'beneficiaries',
            'beneficiaries.beneficiary:id,type'
        ]);
    }

    public function store()
    {
        DB::transaction(function () {
            $this->service
                ->setAttrs(\request()->only('consider_type', 'payrun_period', 'consider_overtime',
                    'departments', 'users', 'executable_month', 'executable_year', 'end_date', 'start_date',
                    'allowances', 'allowanceValues', 'allowancePercentages',
                    'deductions', 'deductionValues', 'deductionPercentages',
                    'note'
                ))->validations()
                ->setRanges()
                ->dateRangeValidations()
                ->setUsers()
                ->saveAndSetManualPayrun()
                ->saveAndSetBeneficiaries()
                ->generateManualPayrunPayslips()
                ->saveBatchId();
        });

        return created_responses('payrun');
    }

    public function update(Payrun $payrun)
    {
        DB::transaction(function () use ($payrun) {
            $this->service
                ->validateForUpdate($payrun)
                ->setAttrs(\request()->only('consider_type', 'payrun_period', 'consider_overtime',
                    'departments', 'users', 'executable_month', 'executable_year', 'end_date', 'start_date',
                    'allowances', 'allowanceValues', 'allowancePercentages',
                    'deductions', 'deductionValues', 'deductionPercentages',
                    'note'
                ))->validations()
                ->setRanges()
                ->dateRangeValidations()
                ->setUsers()
                ->updatePayrun($payrun)
                ->setPayrun($payrun)
                ->deleteAllUnderPayrun()
                ->saveAndSetBeneficiaries()
                ->generateManualPayrunPayslips()
                ->setModel($payrun)
                ->saveBatchId();
        });

        return updated_responses('payrun');
    }

}
