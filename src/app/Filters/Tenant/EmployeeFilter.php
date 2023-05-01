<?php


namespace App\Filters\Tenant;


use App\Filters\Core\traits\CreatedByFilter;
use App\Filters\Core\UserFilter;
use App\Filters\Traits\DateRangeFilter;
use App\Helpers\Traits\MakeArrayFromString;
use App\Helpers\Traits\UserAccessQueryHelper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class EmployeeFilter extends UserFilter
{
    use DateRangeFilter, CreatedByFilter, UserAccessQueryHelper;

    use MakeArrayFromString;

    public function designations($designations = null): void
    {
        $designations = $this->makeArray($designations);

        $this->builder->when(count($designations), function (Builder $builder) use ($designations) {
            $builder->whereHas(
                'designation',
                fn(Builder $b) => $b->whereIn('id', $designations)
            );
        });
    }

    public function departments($departments = null): void
    {
        $departments = $this->makeArray($departments);

        $this->builder->when(count($departments), function (Builder $builder) use ($departments) {
            $builder->whereHas(
                'department',
                fn(Builder $b) => $b->whereIn('id', $departments)
            );
        });
    }

    public function workingShifts($workingShifts = null): void
    {
        $workingShifts = $this->makeArray($workingShifts);

        $this->builder->when(count($workingShifts), function (Builder $builder) use ($workingShifts) {
            $builder->whereHas(
                'workingShift',
                fn(Builder $b) => $b->whereIn('id', $workingShifts)
            );
        });
    }

    public function employmentStatuses($employmentStatus = null): void
    {
        $employmentStatus = $this->makeArray($employmentStatus);

        $this->builder->when(count($employmentStatus), function (Builder $builder) use ($employmentStatus) {
            $builder->whereHas(
                'employmentStatus',
                fn(Builder $b) => $b->whereIn('id', $employmentStatus)
            );
        });
    }

    public function roles($roles = null): void
    {
        $roles = $this->makeArray($roles);

        $this->builder->when(count($roles), function (Builder $builder) use ($roles) {
            $builder->whereHas(
                'roles',
                fn(Builder $b) => $b->whereIn('id', $roles)
            );
        });
    }

    public function joiningDate($date = null): void
    {
        $date = json_decode(htmlspecialchars_decode($date), true);

        $this->builder->when($date, function (Builder $builder) use ($date) {
            $builder->whereHas(
                'profile',
                fn(Builder $b) => $b->whereBetween(DB::raw('DATE(joining_date)'), array_values($date))
            );
        });
    }

//    public function all($all = 'yes'): void
//    {
//        $this->builder->when($all == 'no', function (Builder $builder) {
//            $builder->where('id', auth()->id());
//        });
//    }

    public function showAll($showAll = 'yes')
    {
        $this->builder->when($showAll == 'no', function (Builder $builder) {
            $builder->where('id', auth()->id());
        }, function (Builder $builder) {
            $builder->when(request()->get('access_behavior') == 'own_departments',
                fn(Builder $b) => $this->userAccessQuery($b, 'id')
            );
        });
    }

    public function gender($gender = null)
    {
        $genders = $this->makeArray($gender);
        $this->builder->when($gender, function (Builder $builder) use ($genders) {
            $builder->whereHas(
                'profile',
                fn(Builder $b) => $b->whereIn('gender', $genders)
            );
        });
    }

    public function salary($salary = null)
    {
        $salaryRange = json_decode(htmlspecialchars_decode($salary), true);
        $this->builder->when($salary, function (Builder $builder) use ($salaryRange) {
            $builder->where(function (Builder $b) use ($salaryRange) {
                $b->whereHas(
                    'updatedSalary',
                    fn(Builder $builder) => $builder->whereBetween('amount', array_values($salaryRange))
                )->orWhereHas(
                    'salary',
                    fn(Builder $builder) => $builder->whereBetween('amount', array_values($salaryRange))
                );
            });
        });
    }

    public function search($search = null)
    {
        return $this->builder->when($search, function (Builder $builder) use ($search) {
            $builder->where(function (Builder $builder) use ($search) {
                $builder->where('first_name', 'LIKE', "%$search%")
                    ->orWhere('last_name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%$search%")
                    ->orWhereRaw(DB::raw('CONCAT(`first_name`, " ", `last_name`) LIKE ?'), ["%$search%"]);
            });
        });
    }

}
