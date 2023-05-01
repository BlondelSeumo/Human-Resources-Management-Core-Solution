<?php


namespace App\Hooks\Settings;


use App\Helpers\Core\Traits\InstanceCreator;
use App\Hooks\HookContract;

class BeforeNotificationSettingSaved extends HookContract
{
    use InstanceCreator;

    public function handle()
    {
        validator(request()->all(),[
            'notify_by' => 'required|array',
        ],[
            'notify_by.required' => __t('the_notification_channel_field_is_required')
        ])->validate();
    }
}