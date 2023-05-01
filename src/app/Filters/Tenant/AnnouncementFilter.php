<?php


namespace App\Filters\Tenant;


use App\Filters\Core\traits\NameFilter;
use App\Filters\Core\traits\SearchFilterTrait;
use App\Filters\FilterBuilder;
use App\Filters\Traits\DateRangeFilter;
use App\Filters\Core\traits\CreatedByFilter;
use App\Helpers\Traits\MakeArrayFromString;
use Illuminate\Database\Eloquent\Builder;

class AnnouncementFilter extends FilterBuilder
{
    use DateRangeFilter, SearchFilterTrait, CreatedByFilter, NameFilter, MakeArrayFromString;

    public function departments($departments = null): void
    {
        $departments = $this->makeArray($departments);

        $this->builder->when(count($departments), function (Builder $builder) use ($departments) {
            $builder->whereHas(
                'departments',
                fn(Builder $b) => $b->whereIn('id', $departments)
            );
        });
    }
}
