<?php


namespace App\Filters\Core;


use App\Filters\Core\traits\AllowedResourceFilter;
use App\Filters\Core\traits\ContextFilter;
use App\Filters\Core\traits\CreatedByFilter;
use App\Filters\Core\traits\NameFilter;
use App\Filters\Core\traits\SearchFilterTrait;
use App\Filters\Core\traits\StatusIdFilter;
use App\Filters\Core\traits\TypeFilter;
use App\Filters\FilterBuilder;

class BaseFilter extends FilterBuilder
{
    use NameFilter,
        CreatedByFilter,
        TypeFilter,
        StatusIdFilter,
        ContextFilter,
        AllowedResourceFilter,
        SearchFilterTrait;
}
