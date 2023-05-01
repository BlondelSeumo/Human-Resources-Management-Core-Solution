<?php


namespace App\Mail\Tag;


class RoleTag extends Tag
{
    protected $role;

    public function __construct($role, $notifier, $receiver)
    {
        $this->role = $role;
        $this->notifier = $notifier;
        $this->receiver = $receiver;
        $this->resourceurl = optional(tenant())->id ?
            route('support.tenant.users',optional(tenant())->is_single ? '' : ['tenant_parameter' => tenant()->short_name ])
            : route(config('notification.user_front_end_route_name'));
    }

    public function notification()
    {
        return  array_merge([
            '{name}' => $this->role->name,
        ], $this->common());
    }
}
