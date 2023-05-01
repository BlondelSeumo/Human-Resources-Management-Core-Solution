<?php


namespace App\Mail\Tag;


class SettingTag extends Tag
{
    protected $setting_name;

    public function __construct($setting_name, $notifier, $receiver)
    {
        $this->setting_name = $setting_name;
        $this->notifier = $notifier;
        $this->receiver = $receiver;
        $this->resourceurl = optional(tenant())->id ?
            route('support.tenant.settings', optional(tenant())->is_single ? '' : ['tenant_parameter' => tenant()->short_name ])
            : config('notification.settings_front_end_route_name');
    }
    public function notification()
    {
        return  array_merge([
            '{name}' => $this->setting_name,
        ], $this->common());
    }
}
