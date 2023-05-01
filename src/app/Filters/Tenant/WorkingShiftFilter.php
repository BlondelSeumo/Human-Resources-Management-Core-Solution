<?php


namespace App\Filters\Tenant;


use App\Filters\Core\traits\NameFilter;
use App\Filters\Core\traits\SearchFilterTrait;
use App\Filters\FilterBuilder;
use App\Filters\Traits\DateRangeFilter;
use App\Filters\Traits\StatusFilterTrait;
use App\Helpers\Traits\MakeArrayFromString;
use Illuminate\Database\Eloquent\Builder;

class WorkingShiftFilter extends FilterBuilder
{
    use DateRangeFilter, StatusFilterTrait, SearchFilterTrait, NameFilter, MakeArrayFromString;

    public function type($type = '')
    {
        $types = $this->makeArray($type);

        $this->builder->when($type && count($types), fn (Builder $builder) => $builder->whereIn('type', $types));
    }

}
