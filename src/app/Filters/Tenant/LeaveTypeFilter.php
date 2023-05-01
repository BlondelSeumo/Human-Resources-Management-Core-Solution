<?php


namespace App\Filters\Tenant;


use App\Filters\Core\traits\NameFilter;
use App\Filters\Core\traits\SearchFilterTrait;
use App\Filters\FilterBuilder;
use App\Filters\Traits\DateRangeFilter;
use Illuminate\Database\Eloquent\Builder;

class LeaveTypeFilter extends FilterBuilder
{
    use DateRangeFilter, NameFilter, SearchFilterTrait;

    public function type($type = null)
    {
        $this->builder->when($type, function (Builder $builder) use ($type) {
            $builder->whereIn('type', explode(',', $type));
        });
    }
}
