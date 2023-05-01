<?php


namespace App\Filters\Core;


use App\Filters\Core\traits\NameFilter;
use App\Filters\Core\traits\SearchFilterTrait;
use App\Filters\Core\traits\TypeFilter;
use App\Filters\FilterBuilder;
use Illuminate\Database\Eloquent\Builder;

class NotificationEventFilter extends FilterBuilder
{
    use NameFilter, SearchFilterTrait, TypeFilter;

    public function from($settings = null)
    {
        $this->builder->when($settings === 'settings', function (Builder $builder) {
            $builder->whereNotIn('name', ['invite_user', 'password_reset']);
        });
    }

}
