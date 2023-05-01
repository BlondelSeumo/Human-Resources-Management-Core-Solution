<?php

namespace App\Http\Controllers\Tenant\Auth;

use App\Filters\Common\Auth\UserFilter as AppUserFilter;
use App\Filters\Core\UserFilter;
use App\Filters\Tenant\Helper\UserSelectableAccessFilter;
use App\Http\Controllers\Controller;
use App\Models\Core\Auth\User;
use App\Repositories\Core\Status\StatusRepository;
use Illuminate\Database\Eloquent\Builder;

class TenantUserAPIController extends Controller
{
    /**
     * @var UserSelectableAccessFilter
     */
    private UserSelectableAccessFilter $userAccessSelectableFilter;

    public function __construct(UserFilter $filter, UserSelectableAccessFilter $userAccessSelectableFilter)
    {
        $this->filter = $filter;
        $this->userAccessSelectableFilter = $userAccessSelectableFilter;
    }

    public function index()
    {
        $statuses_id = resolve(StatusRepository::class)->userInvitedInactive();
        return (new AppUserFilter(
            User::query()
                ->select(['id', 'first_name', 'last_name', 'email', 'status_id'])
                ->when(request()->has('with') && request()->has('with') == 'status-profile-picture', function (Builder $builder){
                    $builder->with(['status:id,name,class', 'profilePicture']);
                })->when(request()->has('except-invited-only'), function (Builder $builder) use ($statuses_id){
                    $builder->where('status_id','!=',  resolve(StatusRepository::class)->userInvited());
                }, function (Builder $builder) use ($statuses_id){
                    $builder->whereNotIn('status_id',  $statuses_id);
                })
                ->when(request()->has('existing'), function(Builder $builder) {
                    $builder->whereNotIn('id', explode(',', request('existing')));
                })
                ->when(optional(tenant())->is_single, function (Builder $builder) {
                    $builder->whereHas('roles', function (Builder $builder) {
                        $builder->where('tenant_id', tenant()->id);
                    });
                }, function (Builder $builder) {
                    $builder->whereHas('tenants', function (Builder $builder) {
                        $builder->where('id', tenant()->id);
                    });
                })->when(request()->include_profile_picture, function (Builder $builder) {
                    $builder->with('profilePicture');
                })
        ))->filter()
            ->filters($this->filter)
            ->filters($this->userAccessSelectableFilter)
            ->get();
    }

    public function nextUser($user, $type)
    {
        $userInvited = resolve(StatusRepository::class)->userInvited();

        return User::where('id', '!=', $user)
            ->with('profilePicture', 'status:id,name,class')
            ->when(
                $type == 'next',
                fn(Builder $builder) => $builder->where('id', '>', $user)->orderBy('id', 'ASC'),
                fn(Builder $builder) => $builder->where('id', '<', $user)->orderBy('id', 'DESC')
            )->where('status_id', '!=', $userInvited)
            ->firstOrFail();
    }
}
