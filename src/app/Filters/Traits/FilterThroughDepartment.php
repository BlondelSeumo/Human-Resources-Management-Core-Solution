<?php


namespace App\Filters\Traits;


use App\Helpers\Traits\MakeArrayFromString;
use Illuminate\Database\Eloquent\Builder;

trait FilterThroughDepartment
{
    use MakeArrayFromString;

    public function departments($departments = null): void
    {
        $departments = $this->makeArray($departments);

        $this->builder->when(count($departments), function (Builder $builder) use ($departments){
            $builder->whereHas('user', function (Builder $builder) use ($departments){
                $builder->whereHas('department', function (Builder $builder) use ($departments){
                    $builder->whereIn('id', $departments);
                });
            });
        });
    }
}