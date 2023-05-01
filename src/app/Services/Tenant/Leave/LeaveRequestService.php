<?php

namespace App\Services\Tenant\Leave;

use App\Models\Core\Auth\User;
use App\Models\Tenant\Employee\Department;
use App\Services\Tenant\TenantService;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Query\Builder;

class LeaveRequestService extends TenantService
{
    protected User $user;

    public function getDepartmentAndUsers(): array
    {
        $departments = [];
        $users = [];

        $data = Department::with('users:id')
            ->where('manager_id', $this->user->id)
            ->get('id');

        if ($data->count()) {
            $departments = $data->pluck('id')->toArray();
            $users = $data->map(fn(Department $department) => $department->users->pluck('id')->toArray())
                ->flatten()
                ->values()
                ->toArray();
        }

        return ['departments' => $departments, 'users' => $users];

    }

    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function relations(): array
    {
        return [
            'user:id,first_name,last_name,status_id',
            'user.department:id,name',
            'user.profilePicture',
            'user.status:id,name,class',
            'lastReview',
            'lastReview.department:id,manager_id',
            'status:id,name,class',
            'type:id,name',
            'attachments',
            'comments' => fn(MorphMany $many) => $many->orderBy('parent_id', 'DESC')
                ->select('id','commentable_type','commentable_id','user_id','type','comment', 'parent_id'),
        ];
    }


}
