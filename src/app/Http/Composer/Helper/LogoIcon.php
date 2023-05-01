<?php


namespace App\Http\Composer\Helper;


use App\Helpers\Core\Traits\InstanceCreator;

class LogoIcon
{
    use InstanceCreator;

    public function logoIcon()
    {
        $logo = empty(settings('tenant_logo', 'app_logo'))
            ? url('/images/logo.png') :
            url(settings('tenant_logo', 'app_logo'));

        $icon = empty(settings('tenant_icon', 'app_icon')) ?
            url('/images/icon.png') :
            url(settings('tenant_icon', 'app_icon'));

        return [
            'logo' => $logo,
            'icon' => $icon
        ];
    }
}
