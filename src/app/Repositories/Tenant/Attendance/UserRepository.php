<?php


namespace App\Repositories\Tenant\Attendance;


use App\Filters\Common\Auth\UserFilter;
use App\Filters\FilterBuilder;
use App\Models\Core\Auth\User;
use App\Repositories\Core\BaseRepository;
use App\Repositories\Core\Status\StatusRepository;

class UserRepository extends BaseRepository
{
    protected array $relationships = [];

    protected FilterBuilder $filter;

    public function get()
    {
        $userInvited = resolve(StatusRepository::class)->userInvited();

        $builder = User::filters($this->filter)
            ->with($this->relationships)
            ->select(['id', 'first_name', 'last_name']);

        return (new UserFilter($builder))
            ->filter()
            ->where('status_id', '!=', $userInvited)
            ->paginate(request()->get('per_page', 15));
    }


    public function setRelationships(array $relationships): UserRepository
    {
        $this->relationships = $relationships;

        return $this;
    }

    public function setFilter(FilterBuilder $filter): UserRepository
    {
        $this->filter = $filter;
        return $this;
    }
}