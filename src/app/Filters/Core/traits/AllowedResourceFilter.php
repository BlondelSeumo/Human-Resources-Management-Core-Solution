<?php


namespace App\Filters\Core\traits;


use Illuminate\Database\Eloquent\Builder;

trait AllowedResourceFilter
{
    public function allowedResource()
    {
        $meta = request()->allowed_resource;
        $this->builder->when(count($meta), function (Builder $query) use ($meta) {
            $query->whereIn('id', $meta);
        });
    }
}
