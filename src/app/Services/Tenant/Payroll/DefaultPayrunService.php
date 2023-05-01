<?php

namespace App\Services\Tenant\Payroll;

use App\Exceptions\GeneralException;
use App\Helpers\Core\Traits\Memoization;
use App\Helpers\Traits\SettingKeyHelper;
use App\Models\Core\Auth\User;
use App\Models\Tenant\Employee\Department;
use App\Models\Tenant\Payroll\Payrun;
use App\Models\Tenant\Payroll\PayrunType;
use App\Repositories\Core\Status\StatusRepository;
use App\Services\Tenant\TenantService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class DefaultPayrunService extends TenantService
{
    use Memoization, SettingKeyHelper;

    public function restrictedUserForBadge(): array
    {
        return $this->memoize('badge-restricted-user', function (){
            $settings = (object)$this->getSettingFromKeys('beneficiary')('users', 'eligible_audience', 'departments', 'employment_statuses');

            if (!(array)$settings || optional($settings->eligible_audience) == 'all') return [];

            $departments = json_decode($settings->departments);
            $users = json_decode($settings->users);
            $employmentStatuses = json_decode($settings->employment_statuses);

            if (!count($departments) && !count($employmentStatuses)) return $users;

            $departmentUsers = $this->departmentsUsers($departments);

            $employmentStatusesUsers = User::query()
                ->whereHas('employmentStatus', fn (Builder $builder) =>
                    $builder->whereIn('id', $employmentStatuses)
                )->pluck('id')
                ->toArray();

            return array_unique(array_merge($departmentUsers, $users, $employmentStatusesUsers));
        }, $this->refreshMemoization);
    }

    public function restrictedUser()
    {
        return $this->memoize('payrun-restricted-user', function (){
            $settings = (object)$this->getSettingFromKeys('payrun')('users', 'eligible_audience', 'departments');

            if (!(array)$settings || optional($settings->eligible_audience) == 'all') return [];

            $departments = json_decode($settings->departments);
            $users = json_decode($settings->users);

            if (!count($departments)) return $users;

            $departmentUsers = $this->departmentsUsers($departments);

            return array_unique(array_merge($departmentUsers, $users));
            }, $this->refreshMemoization);
    }

    public function departmentsUsers($departments): array
    {
        return Department::query()
            ->whereIn('id', $departments)
            ->with('users:id')
            ->get()
            ->pluck('users')
            ->flatten()
            ->pluck('id')
            ->toArray();
    }

    public function eligibleUser()
    {
        $status_id = resolve(StatusRepository::class)->userActive();

        return  User::query()->where('is_in_employee', 1)
            ->where('status_id',  $status_id)
            ->whereNotIn('id', $this->restrictedUser());
    }

    public function followedByEmployee()
    {
        return $this->eligibleUser()
            ->where(function (Builder $builder){
               $builder->whereHas('payrunSetting')
                   ->orWhereHas('payrunBeneficiaries')
                   ->orWhereIn('id', $this->restrictedUserForBadge());
            });
    }

    public function countFollowedByEmployee(): int
    {
        return (int) $this->followedByEmployee()->count();
    }
    
    public function countFollowedBySettings(): int
    {
        return (int) $this->followedBySettings()->count();
    }

    public function followedBySettings()
    {
        return $this->eligibleUser()
            ->whereDoesntHave('payrunSetting')
            ->whereDoesntHave('payrunBeneficiaries')
            ->whereNotIn('id', $this->restrictedUserForBadge());
    }

    public static function defaultSettings()
    {
        $defaultPayrun = PayrunType::getDefault();
        throw_if(
            !$defaultPayrun,
            new GeneralException(__t('default_payrun_settings_not_set'))
        );
        return $defaultPayrun;
    }

    public function getDefaultPayrunTimeRange(): array
    {
        return $this->getPreviousTimeRange(
            $this->defaultSettings()->setting->payrun_period
        );
    }

    public function getPreviousMonthDateRange(Carbon $date): array
    {
        $previousMonthStartDate = $date->startOfMonth()->subMonthsNoOverflow()->toDateString();
        $previousMonthEndDate = $date->endOfMonth()->toDateString();

        return [$previousMonthStartDate, $previousMonthEndDate];
    }

    public function getPreviousWeekDateRange(Carbon $date): array
    {
        $previousWeekStartDate = $date->subWeek()->startOfWeek()->toDateString();
        $previousWeekEndDate = $date->endOfWeek()->toDateString();

        return [$previousWeekStartDate, $previousWeekEndDate];
    }

    public function employeesConsiderType(): string
    {
        $employeeConsiderTypes = $this->uniqueEmployeesSettingByKey('consider_type');

        return count($employeeConsiderTypes) === 1 ? $employeeConsiderTypes[0] : implode(', ' , $employeeConsiderTypes);
    }

    public function uniqueEmployeesSettingByKey($type = 'consider_type'): array
    {
        $pluckString = [
            'consider_type' => 'payrunSetting.consider_type',
            'payrun_period' => 'payrunSetting.payrun_period',
            'consider_overtime' => 'payrunSetting.consider_overtime'
        ][$type];

        $settingsValue = [
            'consider_type' => $this->defaultSettings()->setting->consider_type,
            'payrun_period' => $this->defaultSettings()->setting->payrun_period,
            'consider_overtime' => $this->defaultSettings()->setting->consider_overtime
        ][$type];

        return array_unique(
            array_map(function ($value) use ($settingsValue, $type){
                return $type == 'consider_overtime' ?
                    $value ? __t('include') : __t('exclude') :
                    __t($value ?? $settingsValue);
            }, array_unique(
                $this->followedByEmployee()
                    ->with('payrunSetting')
                    ->get()
                    ->pluck($pluckString)
                    ->toArray()
            )));
    }

    public function employeesPayrunPeriod(): string
    {
        $employeePayrunPeriod = $this->uniqueEmployeesSettingByKey('payrun_period');

        return count($employeePayrunPeriod) == 1 ? $employeePayrunPeriod[0] : implode(',' , $employeePayrunPeriod);
    }

    public function employeesPayrunConsiderOvertime(): string
    {
        $employeePayrunConsiderOvertime = $this->uniqueEmployeesSettingByKey('consider_overtime');

        return count($employeePayrunConsiderOvertime) == 1 ? $employeePayrunConsiderOvertime[0] : implode(',' , $employeePayrunConsiderOvertime);

    }

    public function employeePayrunTimeRange(): array
    {
        return count($this->uniqueEmployeesSettingByKey('payrun_period')) > 1? [] :
            $this->getPreviousTimeRange($this->employeesPayrunPeriod());
    }

    public function getPreviousTimeRange($type): array
    {
        $nowDate = nowFromApp();

        return $type == 'weekly' ?
            $this->getPreviousWeekDateRange($nowDate) :
            $this->getPreviousMonthDateRange($nowDate);
    }

    public function getDefaultBeneficiaryBadges()
    {
        return $this->defaultSettings()->load(['beneficiaries', 'beneficiaries.beneficiary'])->beneficiaries;
    }

}
