<?php


namespace App\Filters\Core;


use App\Filters\Core\traits\NameFilter;
use App\Filters\Core\traits\SearchFilterTrait;
use App\Filters\Core\traits\TypeFilter;
use App\Filters\FilterBuilder;
use Illuminate\Database\Eloquent\Builder;

class RoleFilter extends FilterBuilder
{
    use NameFilter, SearchFilterTrait, TypeFilter;

    public function except($except = null)
    {
        $this->builder->when($except == 'admin', function (Builder $builder) {
            $builder->where('is_admin', 0);
        })->when($except == 'default', function (Builder $builder) {
            $builder->where('is_default', 0);
        });
    }
}
