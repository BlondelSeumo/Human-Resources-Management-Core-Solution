<?php


namespace App\Filters\Core;


use App\Filters\Core\traits\StatusIdFilter;
use App\Filters\FilterBuilder;
use App\Filters\Traits\UserFilterTrait;
use App\Helpers\Traits\MakeArrayFromString;
use App\Models\Tenant\WorkingShift\WorkingShiftUser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class UserFilter extends FilterBuilder
{
    use StatusIdFilter, UserFilterTrait, MakeArrayFromString;

    public function firstName($first_name = null)
    {
        $this->whereClause('first_name', "%{$first_name}%", 'LIKE');
    }

    public function lastName($last_name = null)
    {
        $this->whereClause('last_name', "%{$last_name}%", 'LIKE');
    }

    public function email($email = null)
    {
        $this->whereClause('email', "%{$email}%", 'LIKE');
    }

    public function search($search = null)
    {
        return $this->builder->when($search, function (Builder $builder) use ($search) {
            $builder->where('first_name', 'LIKE', "%$search%")
                ->orWhere('last_name', 'LIKE', "%{$search}%")
                ->orWhere('email', 'LIKE', "%$search%")
                ->orWhereRaw(DB::raw('CONCAT(`first_name`, " ", `last_name`) LIKE ?'), ["%$search%"]);
        });
    }

    public function type($type = null)
    {
        return $this->typeFilter($type);
    }

    public function excludedDepartments($departments = null): void
    {
        $departments = $this->makeArray($departments);

        $this->builder->when(count($departments), function (Builder $builder) use ($departments) {
            $builder->whereHas('department', function (Builder $builder) use ($departments) {
                $builder->whereNotIn('id', $departments);
            });
        });
    }

    public function excludedWorkingShifts($workingShifts = null): void
    {
        $workingShifts = $this->makeArray($workingShifts);

        $users = WorkingShiftUser::whereIn('working_shift_id', $workingShifts)
            ->whereNull('end_date')
            ->pluck('user_id')
            ->toArray();

        $this->builder->when(count($users), function (Builder $builder) use ($users) {
            $builder->whereNotIn('id', $users);
        });
    }

    public function employee($employee = 'only'): void
    {
        $this->builder->when(
            $employee == 'only',
            fn(Builder $builder) => $builder->where('is_in_employee', 1)
        );
    }

}
