<?php


namespace App\Filters\Traits;


use Illuminate\Database\Eloquent\Builder;

trait UserFilterTrait
{
    public function typeFilter($type = null)
    {
        return $this->builder->when($type, function (Builder $builder) use ($type) {
            $builder->whereHas('roles', function (Builder $builder) use ($type) {
                $builder->when($type == 'app', function (Builder $builder) {
                    $builder->whereNull('brand_id');
                }, function (Builder $builder) {
                    $builder->whereNotNull('brand_id');
                });
            });
        });
    }
}
