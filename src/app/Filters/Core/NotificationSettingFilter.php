<?php


namespace App\Filters\Core;


use App\Filters\Core\traits\CreatedByFilter;
use App\Filters\Core\traits\NameFilter;
use App\Filters\Core\traits\SearchFilterTrait;
use App\Filters\FilterBuilder;

class NotificationSettingFilter extends FilterBuilder
{
    use NameFilter, CreatedByFilter, SearchFilterTrait;

}
