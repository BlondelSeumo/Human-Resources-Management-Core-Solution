<?php


namespace App\Filters\Core;


use App\Filters\FilterBuilder;
use Illuminate\Database\Eloquent\Builder;

class ActivityLogFilter extends FilterBuilder
{
    public function search($search = null)
    {
        $this->builder->when($search, function (Builder $query) use ($search){
            $query->where('description', 'LIKE', "%$search%");
        });
    }
}
