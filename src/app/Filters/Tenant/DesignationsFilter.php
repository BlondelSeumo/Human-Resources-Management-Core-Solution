<?php

namespace App\Filters\Tenant;


use App\Filters\Core\traits\NameFilter;
use App\Filters\Core\traits\SearchFilterTrait;
use App\Filters\FilterBuilder;
use App\Filters\Traits\DateRangeFilter;
use App\Filters\Traits\DepartmentFilterTrait;

class DesignationsFilter  extends FilterBuilder
{
    use DateRangeFilter, SearchFilterTrait, NameFilter, DepartmentFilterTrait;
}
