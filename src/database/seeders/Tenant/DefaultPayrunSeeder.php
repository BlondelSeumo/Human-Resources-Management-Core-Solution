<?php

namespace Database\Seeders\Tenant;

use App\Models\Core\Auth\User;
use App\Repositories\Core\Status\StatusRepository;
use App\Services\Tenant\Payroll\ManualPayrollService;
use App\Services\Tenant\Payroll\PayrunService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DefaultPayrunSeeder extends Seeder
{

    public function run()
    {
        resolve(PayrunService::class)->runDefaultPayrun();

        DB::transaction(function () {
            resolve(ManualPayrollService::class)
                ->setAttrs([
                    'consider_type' => 'none',
                    'payrun_period' => 'monthly',
                    'consider_overtime' => true,
                    'users' => User::query()
                        ->where('status_id', resolve(StatusRepository::class)->userActive())
                        ->limit(3)->latest()->pluck('id')->toArray(),
                    'executable_month' => Carbon::now()->subMonths(2)->format('M'),
                    'executable_year' => Carbon::now()->subMonths(2)->format('Y'),
                    'allowances' => [],
                    'allowanceValues' => [],
                    'allowancePercentages' => [],
                    'deductions' => [],
                    'deductionValues' => [],
                    'deductionPercentages' => [],

                ])
                ->validations()
                ->setRanges()
                ->dateRangeValidations()
                ->setUsers()
                ->saveAndSetManualPayrun()
                ->saveAndSetBeneficiaries()
                ->generateManualPayrunPayslips()
                ->saveBatchId();
        });

        DB::transaction(function () {
            resolve(ManualPayrollService::class)
                ->setAttrs([
                    'consider_type' => 'none',
                    'payrun_period' => 'customized',
                    'consider_overtime' => true,
                    'users' => User::query()
                        ->where('status_id', resolve(StatusRepository::class)->userActive())
                        ->limit(2)->latest()->pluck('id')->toArray(),
                    'start_date' => Carbon::now()->subMonths(2)->startOfMonth()->toDateString(),
                    'end_date' => Carbon::now()->subMonths(2)->startOfMonth()->addDays(10)->toDateString(),
                    'allowances' => [],
                    'allowanceValues' => [],
                    'allowancePercentages' => [],
                    'deductions' => [],
                    'deductionValues' => [],
                    'deductionPercentages' => [],
                ])
                ->validations()
                ->setRanges()
                ->dateRangeValidations()
                ->setUsers()
                ->saveAndSetManualPayrun()
                ->saveAndSetBeneficiaries()
                ->generateManualPayrunPayslips()
                ->saveBatchId();
        });
    }
}
