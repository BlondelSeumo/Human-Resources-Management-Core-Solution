<?php


namespace App\Helpers\Traits;


use App\Models\Core\Auth\User;
use App\Repositories\Tenant\Employee\DepartmentRepository;
use Illuminate\Database\Eloquent\Builder;

trait UserAccessQueryHelper
{
    public function userAccessQuery($builder, $key = 'user_id', $withAuth = true){
        /** @var User $user */
        $user = auth()->user();

        $deptUsers = resolve(DepartmentRepository::class)->getDepartmentUsers($user->id);

        $builder->where(fn(Builder $builder) => $builder
            ->whereIn($key, $deptUsers)
            ->when($withAuth, fn(Builder $b) => $b->orWhere($key, auth()->id()))
        );
    }
}