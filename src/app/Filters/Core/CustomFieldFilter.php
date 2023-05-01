<?php


namespace App\Filters\Core;


use App\Filters\Core\traits\NameFilter;
use App\Filters\Core\traits\SearchFilterTrait;
use App\Filters\FilterBuilder;
use Illuminate\Database\Eloquent\Builder;

class CustomFieldFilter extends FilterBuilder
{
    use NameFilter, SearchFilterTrait;

    public function context($context = null)
    {
        $this->builder->when($context, function (Builder $builder) use ($context) {
            $builder->where('context', $context);
        });
    }
}
