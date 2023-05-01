<?php


namespace App\Filters\Tenant;


use App\Helpers\Traits\DateTimeHelper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class LeaveRequestFilter extends LeaveStatusFilter
{
    use DateTimeHelper;
    public function applyDate($date = null)
    {
        $this->builder->when(
            $date,
            fn(Builder $builder) => $builder->whereDate('created_at', $this->carbon($date)->parse()),
        );
    }

}
