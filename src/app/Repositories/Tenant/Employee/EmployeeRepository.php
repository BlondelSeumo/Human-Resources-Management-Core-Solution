<?php


namespace App\Repositories\Tenant\Employee;


use App\Filters\Common\Auth\UserFilter;
use App\Helpers\Traits\UserAccessQueryHelper;
use App\Models\Core\Auth\User;
use App\Repositories\Core\Status\StatusRepository;
use App\Repositories\Tenant\TenantRepository;
use Illuminate\Database\Eloquent\Builder;

class EmployeeRepository extends TenantRepository
{
    use UserAccessQueryHelper;

    public function getFirstEmployee()
    {
        $userInvited = resolve(StatusRepository::class)->userInvited();

        return (new UserFilter(User::query()
            ->with('profilePicture', 'status:id,name,class')
            ->where('status_id', '!=', $userInvited)
            ->when(request()->get('access_behavior') == 'own_departments',
                fn(Builder $builder) => $this->userAccessQuery($builder, 'id')
            )))->filter()
            ->first(['id', 'first_name', 'last_name', 'email']);
    }
}