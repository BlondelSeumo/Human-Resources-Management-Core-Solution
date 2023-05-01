<?php


namespace App\Mail\Tag;


abstract class Tag
{
    protected $notifier;

    protected $receiver;

    protected $resourceurl;

    abstract function notification();

    public function common()
    {
        return array_merge([
            '{action_by}' => optional($this->notifier)->full_name,
            '{receiver_name}' => optional($this->receiver)->full_name,
            '{resource_url}' => $this->resourceurl
        ], $this->appNameAndLogo());
    }

    public function appNameAndLogo()
    {
        $app_logo = settings('tenant_logo', 'app_logo');
        $tenant_logo = settings('tenant_logo');

        return [
            '{app_name}' => settings('tenant_name', 'app_name'),
            '{tenant_name}' => settings('tenant_name', 'app_name'),
            '{company_name}' => settings('tenant_name', 'app_name'),
            '{app_logo}' => asset(empty($app_logo) ? '/images/logo.png' : $app_logo),
            '{tenant_logo}' => asset(empty($tenant_logo) ? '/images/logo.png' : $tenant_logo),
            '{company_logo}' => asset(empty($tenant_logo) ? '/images/logo.png' : $tenant_logo)
        ];
    }
}
