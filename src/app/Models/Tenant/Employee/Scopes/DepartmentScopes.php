<?php


namespace App\Models\Tenant\Employee\Scopes;


use App\Repositories\Core\Status\StatusRepository;
use Illuminate\Database\Eloquent\Builder;

trait DepartmentScopes
{
    public function scopeActive(Builder $builder)
    {
        $builder->where('status_id', resolve(StatusRepository::class)->departmentActive());
    }
}
