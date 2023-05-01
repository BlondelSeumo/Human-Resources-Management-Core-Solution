<?php

namespace App\Http\Controllers\Tenant\Auth;

use App\Filters\Common\Auth\UserFilter as AppUserFilter;
use App\Helpers\Traits\UserAccessQueryHelper;
use App\Http\Controllers\Core\Auth\User\UserController;
use Illuminate\Database\Eloquent\Builder;

class TenantUserController extends UserController
{
    use UserAccessQueryHelper;

    public function index()
    {
        return (new AppUserFilter(
            $this->service
                ->filters($this->filter)
                ->select(['id', 'first_name', 'last_name', 'email', 'created_by', 'status_id', 'created_at', 'is_in_employee'])
                ->with('roles:id,name,is_admin,is_default,type_id', 'status', 'profilePicture')
                ->when(request()->get('access_behavior') == 'own_departments',
                    fn(Builder $builder) => $this->userAccessQuery($builder, 'id')
                )->latest()
        ))->filter()
            ->paginate(request()->get('per_page', 10));
    }
}
