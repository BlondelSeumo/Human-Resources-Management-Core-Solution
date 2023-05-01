<?php


namespace App\Filters\Tenant;


use App\Filters\Core\traits\NameFilter;
use App\Filters\Core\traits\SearchFilterTrait;
use App\Filters\FilterBuilder;
use App\Filters\Traits\DateRangeFilter;

class EmploymentStatusFilter extends FilterBuilder
{
    use DateRangeFilter, NameFilter, SearchFilterTrait;
}
