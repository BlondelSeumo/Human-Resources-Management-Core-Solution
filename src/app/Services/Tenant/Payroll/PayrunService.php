<?php


namespace App\Services\Tenant\Payroll;


use App\Helpers\Traits\DateRangeHelper;
use App\Helpers\Traits\DateTimeHelper;
use App\Helpers\Traits\TenantAble;
use App\Jobs\Tenant\RunDefaultPayrunJob;
use App\Models\Core\Auth\User;
use App\Models\Tenant\Attendance\Attendance;
use App\Models\Tenant\Attendance\AttendanceDetails;
use App\Models\Tenant\Holiday\Holiday;
use App\Models\Tenant\Payroll\Payrun;
use App\Models\Tenant\Payroll\Payslip;
use App\Repositories\Core\Setting\SettingRepository;
use App\Repositories\Core\Status\StatusRepository;
use App\Services\Tenant\Attendance\AttendanceSummaryService;
use App\Services\Tenant\Leave\LeaveCalendarService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;

class PayrunService extends DefaultPayrunService
{
    use DateRangeHelper, DateTimeHelper, TenantAble;

    private AttendanceSummaryService $attendanceSummaryService;
    private LeaveCalendarService $leaveCalendarService;
    public $batch;
    private $totalSchedule = 0;

    public function __construct(
        LeaveCalendarService $leaveCalendarService,
        AttendanceSummaryService $attendanceSummaryService
    ){
        $this->leaveCalendarService = $leaveCalendarService;
        $this->attendanceSummaryService = $attendanceSummaryService;
    }

    public function runDefaultPayrun(): self
    {
        $this
            ->makePayrunFollowedBySettings($this->followedBySettings()->get())
            ->saveBatchId()
            ->makePayrunFollowedByEmployee($this->followedByEmployee()->get())
            ->saveBatchId();

        return $this;
    }

    public function saveBatchId():self
    {
        if ($this->batch) $this->model->update(['batch_id' => $this->batch->id]);

        return $this;
    }

    public function defaultSettingsInArray(): array
    {
        $settings = $this->defaultSettings();

        return [
            'consider_type' => $settings->setting->consider_type,
            'period' => $settings->setting->payrun_period,
            'consider_overtime' => $settings->setting->consider_overtime
        ];
    }

    public function defaultBeneficiariesInArray()
    {
        $settings = $this->defaultSettings();

        return $settings
            ->load('beneficiaries', 'beneficiaries.beneficiary:id,type')
            ->beneficiaries
            ->toArray();
    }

    public function makePayrunFollowedBySettings($users): self
    {
        if (!$this->countFollowedBySettings()){
            return $this;
        }

        $settings = $this->defaultSettings();

        $attributes = $this->defaultPayrunAttributes($settings);

        $beneficiaries = $this->defaultBeneficiariesInArray();

        $settings = $this->defaultSettingsInArray();

        $this->generateEmployeesPayslip(
            $this->savePayrun($attributes, $beneficiaries),
            $users,
            $settings,
            $beneficiaries
        );

        return $this;
    }

    public function defaultPayrunAttributes($settings): array
    {
        return [
            'name' => 'payrun_'.nowFromApp(),
            'status_id' => resolve(StatusRepository::class)->payrunGenerated(),
            'data' => json_encode(array_merge([
                'time_range' => $this->getDefaultPayrunTimeRange(),
                'employees' => $this->followedBySettings()->pluck('id')->toArray(),
                'type' => 'default',
                'consider_type' => $settings->setting->consider_type,
                'period' => $settings->setting->payrun_period,
                'consider_overtime' => $settings->setting->consider_overtime,
            ], $this->getCommonPayslipSettingData())),
            'followed' => 'settings',
        ];
    }

    public function savePayrun($attributes = [], $beneficiaries = []): Payrun
    {
        $payrun = new Payrun($attributes);
        $payrun->save();

        if (count($beneficiaries)){
            $payrun->beneficiaries()->createMany($beneficiaries);
        }

        $this->model = $payrun;

        return $payrun;
    }

    public function makePayrunFollowedByEmployee($users): self
    {
        if (!$this->countFollowedByEmployee()){
            return $this;
        }

        $settings = [
            'consider_type' => $this->employeesConsiderType(),
            'period' => $this->employeesPayrunPeriod(),
            'consider_overtime' => $this->employeesPayrunConsiderOvertime(),
        ];

        $attributes = $this->employeePayrunAttributes($settings);

        $this->employeePayrunPayslipGenerated($this->savePayrun($attributes), $users);

        return $this;
    }

    public function employeePayrunPayslipGenerated($payrun, $users, $manualSettings = [], $manualBeneficiaries = [], $ranges = [], $isManual = false): self
    {
        $users->load('payrunSetting', 'payrunBeneficiaries');

        $this->batch = Bus::batch([])->dispatch();

        $users->map(function ($user) use ($payrun, $manualSettings, $manualBeneficiaries, $ranges, $isManual){
            $settings = count($manualSettings) ? $manualSettings : [
                'consider_type' => $user->payrunSetting ?
                    $user->payrunSetting->consider_type :
                    $this->defaultSettingsInArray()['consider_type'],
                'period' => $user->payrunSetting ?
                    $user->payrunSetting->payrun_period :
                    $this->defaultSettingsInArray()['period'],
                'consider_overtime' => $user->payrunSetting ?
                    $user->payrunSetting->consider_overtime :
                    $this->defaultSettingsInArray()['consider_overtime'],

            ];

            $beneficiaries = $manualBeneficiaries ?:
                ($isManual ? [] :
                ($user->payrunBeneficiaries ?
                $user->payrunBeneficiaries->load('beneficiary')->toArray() :
                    (in_array($user->id, $this->restrictedUserForBadge()) ? [] :
                    $this->defaultBeneficiariesInArray())));

            $this->batch->add(new RunDefaultPayrunJob($payrun, $user, $settings, $beneficiaries, $ranges));
//            RunDefaultPayrunJob::dispatch($payrun, $user, $settings, $beneficiaries, $ranges);
            //$this->generatePayslip($payrun, $user, $settings, $beneficiaries, $ranges);
        });

        return $this;
    }

    public function generateEmployeesPayslip($payrun, Collection $users, $settings, $beneficiaries): self
    {
        $this->batch = Bus::batch([])->dispatch();
        $users->map(function ($user) use ($payrun, $settings, $beneficiaries){
            $this->batch->add(new RunDefaultPayrunJob($payrun, $user, $settings, $beneficiaries));
//            RunDefaultPayrunJob::dispatch($payrun, $user, $settings, $beneficiaries);
            //$this->generatePayslip($payrun, $user, $settings, $beneficiaries);
        });

        return $this;
    }

    public function generatePayslip($payrun, User $user, $settings, $beneficiaries, $ranges = []): self
    {
        $ranges = count($ranges) ? $ranges : $this->getPreviousTimeRange($settings['period']);

        $payslip = new Payslip([
            'user_id' => $user->id,
            'status_id' => resolve(StatusRepository::class)->payslipGenerated(),
            'start_date' => $ranges[0],
            'end_date' => $ranges[1],
            'period' => $settings['period'],
            'consider_type' => $settings['consider_type'],
            'consider_overtime' => $settings['consider_overtime'],
            'basic_salary' => optional($user->salary($this->carbon($ranges[0])->parse())->first())->amount ?: 0,
            'net_salary' => $this->countNetSalary($user, $settings, $beneficiaries, $ranges)
        ]);

        $payrun->payslips()->save($payslip);

        if ($payrun->followed == 'employee'){
            $payslip->beneficiaries()->createMany($beneficiaries);
        }

        return $this;
    }

    public function countNetSalary($user, $settings, $beneficiaries, $ranges = [])
    {
        $ranges =count($ranges) ? $ranges : $this->getPreviousTimeRange($settings['period']);

        $ranges = [
            $this->carbon($ranges[0])->parse(),
            $this->carbon($ranges[1])->parse()
        ];

        $salary = optional($user->salary($ranges[0])->first())->amount ?: 0;
        $beneficiaries = $this->countBeneficiaries($beneficiaries, $salary);

        if ($settings['consider_type'] == 'daily_log') {
            $earnSalary = $this->netSalaryDailyLogBased($user, $ranges, $salary);
            $finalSalary = $this->finalSalaryCount($user, $ranges, $settings, $earnSalary, $salary);

            return $finalSalary + $beneficiaries;
        }

        if ($settings['consider_type'] == 'hour') {
            $earnSalary = $this->netSalaryHourBased($user, $ranges, $salary);
            $finalSalary = $this->finalSalaryCount($user, $ranges, $settings, $earnSalary, $salary);

            return $finalSalary + $beneficiaries;
        }

        return $this->netSalaryWithoutConsiderType($user, $ranges, $salary) + $beneficiaries;


    }

    public function finalSalaryCount($user, $ranges, $settings, $earnSalary, $salary)
    {
        $rangeSchedule = $settings['consider_type'] == 'daily_log' ? $this->getTotalScheduledInDay($user, $ranges) :
            $this->getTotalScheduledInSec($user, $ranges);

        $totalSchedule = $settings['consider_type'] == 'daily_log' ? $this->countTotalScheduledDays($user, $ranges) :
            $this->countTotalScheduled($user, $ranges);

        $expectedSalary = $this->calculateSalary($salary, $totalSchedule, $rangeSchedule);

        return $settings['consider_overtime'] ? $earnSalary :
            ($earnSalary > $expectedSalary ? $expectedSalary : $earnSalary);
    }

    public function calculateSalary($salary, $totalSchedule, $work, $paidLeave = 0)
    {
        return (($salary / $totalSchedule)
            * ($work + $paidLeave));
    }

    public function netSalaryWithoutConsiderType($user, $ranges, $salary): float
    {
        return $salary;
    }

    public function netSalaryHourBased($user, $ranges, $salary)
    {
        $paidLeave = $this->countPaidLeave($user, $ranges);

        $totalScheduled = $this->countTotalScheduled($user, $ranges);

        $worked = $this->countTotalWorked($user, $ranges);

        return $this->calculateSalary($salary, $totalScheduled, $worked, $paidLeave);
    }

    public function netSalaryDailyLogBased($user, $ranges, $salary)
    {
        $paidLeaveDate = $this->paidLeaveDays($user, $ranges);

        $totalScheduled = $this->countTotalScheduledDays($user, $ranges);

        $workedDate = $this->totalWorkedDate($user, $ranges);

        $worked = count(array_unique(array_merge($paidLeaveDate, $workedDate)));

        return $this->calculateSalary($salary, $totalScheduled, $worked);
    }

    public function totalWorkedDate($user, $ranges): array
    {
        return array_unique($user->load([
            'attendances' => fn(HasMany $builder) => $builder
                ->where('status_id', resolve(StatusRepository::class)->attendanceApprove())
                ->whereBetween(DB::raw('DATE(in_date)'), $this->convertRangesToStringFormat($ranges))
        ])->attendances->pluck('in_date')->toArray());
    }

    public function paidLeaveDays($user, $ranges)
    {
        return $this->leaveCalendarService
            ->setRanges($ranges)
            ->setEmployeeIds([$user->id])
            ->buildWorkshiftService()
            ->getTotalLeaveDurationInDays($user->load([
                'leaves' => function (HasMany $leave) use($ranges) {
                    $leave->where('status_id', resolve(StatusRepository::class)->leaveApproved())
                        ->whereHas('type', fn (Builder $leaveType) => $leaveType->where('type', 'paid'))
                        ->where(function (Builder $builder) use ($ranges){
                            $builder->whereBetween(DB::raw('DATE(start_at)'), $ranges)
                                ->orWhereBetween(DB::raw('DATE(end_at)'), $ranges);
                        });
                }])->leaves);
    }

    public function countTotalScheduledDays($user, $ranges): int
    {
        return $this->memoize('count-total-schedule-days-'.$user->id, function () use ($user, $ranges){
            $getRanges = $this->getDateRangesByMonthYear(
                $this->carbon($ranges[0])->parse()->monthName,
                $this->carbon($ranges[0])->parse()->year
            );

            $ranges = [
                $this->carbon($getRanges[0])->parse(),
                $this->carbon($getRanges[1])->parse(),
            ];

            return $this->getTotalScheduledInDay($user, $ranges);
        });
    }

    public function getTotalScheduledInDay($user, $ranges)
    {
        return $this->attendanceSummaryService
            ->setModel($user)
            ->setRanges($ranges)
            ->setHolidays(
                $this->attendanceSummaryService
                    ->generateEmployeeHolidaysFromDepartments($user->load('departments')->departments)
                    ->merge(Holiday::generalHolidays($ranges))
            )->getTotalScheduledDays();
    }

    public function countTotalWorkedDays($user, $ranges): int
    {
        return Attendance::query()->where('user_id', $user->id)
            ->where('status_id', resolve(StatusRepository::class)->attendanceApprove())
            ->whereBetween(DB::raw('DATE(in_date)'), $this->convertRangesToStringFormat($ranges))
            ->count();
    }

    public function countTotalWorked($user, $ranges)
    {
        $attendanceApprove = resolve(StatusRepository::class)->attendanceApprove();

        return Attendance::addSelect([
            'id',
            'user_id',
            'worked' => AttendanceDetails::whereColumn('attendance_id', 'attendances.id')
                ->where('status_id', $attendanceApprove)
                ->selectRaw(DB::raw('CAST(SUM(TIME_TO_SEC(TIMEDIFF(out_time, in_time))) AS SIGNED) AS worked')),
        ])->where('user_id', $user->id)
            ->where('status_id', $attendanceApprove)
            ->whereBetween(DB::raw('DATE(in_date)'), $this->convertRangesToStringFormat($ranges))
            ->get()->sum('worked');
    }

    public function countTotalScheduled($user, $ranges): int
    {
        return $this->memoize('count-total-schedule-hour-'.$user->id, function () use ($user, $ranges){
            $getRanges = $this->getDateRangesByMonthYear(
                $this->carbon($ranges[0])->parse()->monthName,
                $this->carbon($ranges[0])->parse()->year
            );

            $ranges = [
                $this->carbon($getRanges[0])->parse(),
                $this->carbon($getRanges[1])->parse(),
            ];

            return $this->getTotalScheduledInSec($user, $ranges);
        });

    }

    public function getTotalScheduledInSec($user, $ranges)
    {
        return $this->attendanceSummaryService
            ->setModel($user)
            ->setRanges($ranges)
            ->setHolidays(
                $this->attendanceSummaryService
                    ->generateEmployeeHolidaysFromDepartments($user->load('departments')->departments)
                    ->merge(Holiday::generalHolidays($ranges))
            )->getTotalScheduled();
    }

    public function countPaidLeave($user, $ranges): int
    {
        return $this->leaveCalendarService
            ->setRanges($ranges)
            ->setEmployeeIds([$user->id])
            ->buildWorkshiftService()
            ->getTotalLeaveDurationInSeconds($user->load([
                'leaves' => function (HasMany $leave) use($ranges) {
                    $leave->where('status_id', resolve(StatusRepository::class)->leaveApproved())
                        ->whereHas('type', fn (Builder $leaveType) => $leaveType->where('type', 'paid'))
                        ->where(function (Builder $builder) use ($ranges){
                            $builder->whereBetween(DB::raw('DATE(start_at)'), $ranges)
                                ->orWhereBetween(DB::raw('DATE(end_at)'), $ranges);
                        });
                }])->leaves);
    }

    public function countBeneficiaries($beneficiaries, $salary)
    {
        $allowance = 0;
        $deduction = 0;

        foreach ($beneficiaries as $beneficiary){
            if ($beneficiary['beneficiary']['type'] == 'allowance'){
                $allowance +=  $beneficiary['is_percentage'] ?
                    (($beneficiary['amount'] / 100) * $salary) :
                    $beneficiary['amount'];
            }else{
                $deduction +=   $beneficiary['is_percentage'] ?
                    (($beneficiary['amount'] / 100) * $salary) :
                    $beneficiary['amount'];
            }
        }

        return $allowance - $deduction;
    }

    public function employeePayrunAttributes($settings): array
    {
        return [
            'name' => 'payrun_'.nowFromApp(),
            'status_id' => resolve(StatusRepository::class)->payrunGenerated(),
            'data' => json_encode(array_merge([
                'time_range' => $this->employeePayrunTimeRange(),
                'employees' => $this->followedByEmployee()->pluck('id')->toArray(),
                'type' => 'default',
                'consider_type' => $settings['consider_type'],
                'period' => $settings['period'],
                'consider_overtime' => $settings['consider_overtime'],
            ], $this->getCommonPayslipSettingData())),
            'followed' => 'employee'
        ];
    }

    public function getCommonPayslipSettingData(): array
    {
        return [
            'note' => $this->getAttr('note'),
            'address' => $this->payslipSettings()['address'],
            'title' => $this->payslipSettings()['title'],
            'logo' => $this->payslipSettings()['logo'],
        ];
    }

    public function payslipSettings(): array
    {
        [$setting_able_id, $setting_able_type] = $this->tenantAble();

        $payslip_settings = (object)resolve(SettingRepository::class)
            ->getFormattedSettings('payslip', $setting_able_type, $setting_able_id);

        return [
            'address' => property_exists($payslip_settings, 'address') ? $payslip_settings->address : '',
            'title' => property_exists($payslip_settings, 'title') ? $payslip_settings->title : '',
            'logo' => property_exists($payslip_settings, 'logo') ? $payslip_settings->logo : '',
        ];
    }

    public function runManualPayrun(): self
    {
        return $this;
    }

    public function getDateRangesByMonthYear($month, $year): array
    {
        $date = $this->carbon($month.' '.$year)->parse();

        return [
            $date->startOfMonth()->toDateString(),
            $date->endOfMonth()->toDateString()
        ];
    }
}