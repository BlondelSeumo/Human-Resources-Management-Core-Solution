<?php

namespace App\Models\Core\Auth\Traits\Scope;

use App\Filters\FilterBuilder;
use App\Repositories\Core\Status\StatusRepository;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class UserScope.
 */
trait UserScope
{
    public function scopeFilters($query, FilterBuilder $filter)
    {
        return $filter->apply($query);
    }

    public function scopeActive(Builder $builder)
    {
        $status = resolve(StatusRepository::class)->userActive();

        $builder->where('status_id', $status);
    }

    public function scopeInActive(Builder $builder)
    {
        $status = resolve(StatusRepository::class)->userInactive();

        $builder->where('status_id', $status);
    }

    public function scopeInvited(Builder $builder)
    {
        $status = resolve(StatusRepository::class)->userInvited();

        $builder->where('status_id', $status);
    }

}
