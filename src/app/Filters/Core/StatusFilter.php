<?php


namespace App\Filters\Core;


use App\Filters\FilterBuilder;
use Illuminate\Database\Eloquent\Builder;

class StatusFilter extends FilterBuilder
{
    public function type($type = null)
    {
        $this->builder->when($type, function (Builder $builder) use ($type) {
            $builder->where('type', $type);
        });
    }
}
