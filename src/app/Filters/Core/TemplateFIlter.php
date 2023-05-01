<?php


namespace App\Filters\Core;


use App\Filters\FilterBuilder;
use Illuminate\Database\Eloquent\Builder;

class TemplateFIlter extends FilterBuilder
{
    public function search($search = null)
    {
        $this->builder->when($search, function (Builder $builder) use ($search) {
            $builder->where('subject', 'LIKE', "%{$search}%");
        });
    }
}
