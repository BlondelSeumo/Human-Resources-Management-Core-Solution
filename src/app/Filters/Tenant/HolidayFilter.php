<?php


namespace App\Filters\Tenant;


use App\Filters\Core\traits\NameFilter;
use App\Filters\Core\traits\SearchFilterTrait;
use App\Filters\FilterBuilder;
use App\Filters\Traits\DateRangeFilter;
use App\Helpers\Traits\MakeArrayFromString;
use App\Repositories\Tenant\Employee\DepartmentRepository;
use Illuminate\Database\Eloquent\Builder;

class HolidayFilter extends FilterBuilder
{
    use DateRangeFilter, NameFilter, SearchFilterTrait, MakeArrayFromString;

    public function departments($departments = null)
    {
        $departments = $this->makeArray($departments);

        $this->builder->when(
            count($departments),
            fn(Builder $query) => $query->whereHas(
                'departments',
                fn(Builder $query) => $query->whereIn('id', $departments)
            )
        );
    }

//    public function timePeriod($period = null)
//    {
//        $period = json_decode(htmlspecialchars_decode($period), true);
//
//        $this->builder->when($period, function (Builder $builder) use ($period) {
//            $builder->where(function (Builder $builder) use ($period) {
//                $builder->whereBetween('start_date', ['2021-03-01', '2021-03-05'])
//                    ->orWhereBetween('end_date', array_values($period))
//                    ->orWhere(function ($query) use ($period) {
//                        $query->whereDate('start_date', '<=', $period['start'])
//                            ->whereDate('end_date', '>=', $period['end']);
//                    });
//            });
//        });
//    }

    public function showAll($showAll = 'yes')
    {
        $user = auth()->user()->load('department');
        $userDeptId = optional($user->department)->id;

        if (!request()->get('departments') && request()->get('access_behavior') == 'own_departments'){
            $this->builder->where(function (Builder $builder) use($userDeptId){
                $builder->whereHas('departments', fn(Builder $b)=> $b->where('id', $userDeptId))
                    ->orWhereDoesntHave('departments');
            });
        }

        $this->builder->where(function (Builder $builder) use($userDeptId, $showAll){
            $builder->WhereHas('departments',function (Builder $builder) use ($userDeptId, $showAll){
                $builder->where(function (Builder $builder) use ($showAll, $userDeptId){
                    $builder->when($showAll == 'yes', fn(Builder $b) => $b
                        ->when(request()->get('access_behavior') == 'own_departments',
                            function (Builder $builder) {
                                $userDepartments = resolve(DepartmentRepository::class)->getDepartments(auth()->id());
                                $builder->whereIn('id', $userDepartments);
                            }),fn(Builder $builder) => $builder->where('id', $userDeptId),
                    );
                })->orWhere('id', $userDeptId);
            })->orWhereDoesntHave('departments');
        });
    }
}
