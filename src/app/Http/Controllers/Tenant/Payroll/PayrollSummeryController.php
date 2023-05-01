<?php

namespace App\Http\Controllers\Tenant\Payroll;

use App\Filters\Tenant\PayslipFilter;
use App\Helpers\Traits\ConflictedPayslipQueryHelpers;
use App\Helpers\Traits\DateRangeHelper;
use App\Http\Controllers\Controller;
use App\Models\Core\Auth\User;
use App\Models\Tenant\Payroll\Payslip;
use App\Repositories\Core\Status\StatusRepository;
use App\Services\Tenant\Payroll\EmployeePayrunService;
use App\Services\Tenant\Payroll\PayslipService;
use Illuminate\Support\Facades\DB;

class PayrollSummeryController extends Controller
{
    use DateRangeHelper, ConflictedPayslipQueryHelpers;

    public function __construct(PayslipService $service, PayslipFilter $filter)
    {
        $this->service = $service;
        $this->filter = $filter;
    }

    public function index(User $employee)
    {
        $this->authorized($employee);

        $within = request()->get('within');
        $month = $within ?: request('month_number') + 1;
        $ranges = $this->convertRangesToStringFormat($this->getStartAndEndOf($month, request()->get('year')));

        if ($within == 'total') {
            $min_date = Payslip::query()->oldest('start_date')->first()->start_date;
            $ranges = [$min_date, todayFromApp()->toDateString()];
        }

        $payslips = Payslip::query()
            ->where('user_id', $employee->id)
            ->with($this->service->getRelations())
            ->whereBetween(DB::raw('DATE(start_date)'), count($ranges) == 1 ? [$ranges[0], $ranges[0]] : $ranges)
            ->whereBetween(DB::raw('DATE(end_date)'), count($ranges) == 1 ? [$ranges[0], $ranges[0]] : $ranges)
            ->latest()
            ->paginate(request()->get('per_page', 12));

        $payslips->map(function ($payslip){
            $conflictedData = $this->conflictedUserPayslip($payslip, $payslip->start_date, $payslip->end_date);
            $payslip->conflicted = $conflictedData->count();
        });

        return $payslips;
    }

    public function summery(User $employee)
    {
        $this->authorized($employee);

        $within = request()->get('within');
        $month = $within ?: request('month_number') + 1;
        $ranges = $this->convertRangesToStringFormat($this->getStartAndEndOf($month, request()->get('year')));

        if ($within == 'total') {
            $min_date = Payslip::query()->oldest('start_date')->first()->start_date;
            $ranges = [$min_date, todayFromApp()->toDateString()];
        }

        $statusSent = resolve(StatusRepository::class)->payslipSent();

        $totalPayslipQuery = $employee->payslips()
            ->whereBetween(DB::raw('DATE(start_date)'), count($ranges) == 1 ? [$ranges[0], $ranges[0]] : $ranges)
            ->whereBetween(DB::raw('DATE(end_date)'), count($ranges) == 1 ? [$ranges[0], $ranges[0]] : $ranges);

        $totalPayslipQueryClone = clone $totalPayslipQuery;
        $totalConflict = $totalPayslipQueryClone->whereIn('id', $this->getConflictedPayslip())->count();

        $totalPayslip = $totalPayslipQuery->get();

        $payslipSent = $totalPayslip->where('status_id', $statusSent);


        $employee->load([
            'department:id,name',
            'profile',
            'profilePicture',
        ]);
        return [
            'card_summaries' => [
                'total' => $totalPayslip->count(),
                'sent' => $payslipSent->count(),
                'conflicted' => $totalConflict,
            ],
            'employee' => $employee
        ];
    }


    public function authorized(User $employee): self
    {
        resolve(EmployeePayrunService::class)
            ->setModel($employee)
            ->validateForEmployeesPayslipsAccess(request()->get('show_all'));

        return $this;
    }
}
