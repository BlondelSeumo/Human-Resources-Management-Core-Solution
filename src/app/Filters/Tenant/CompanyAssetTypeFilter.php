<?php

namespace App\Filters\Tenant;

use App\Filters\Core\traits\NameFilter;
use App\Filters\Core\traits\SearchFilterTrait;
use App\Filters\FilterBuilder;

class CompanyAssetTypeFilter extends FilterBuilder
{
    use SearchFilterTrait, NameFilter;
}