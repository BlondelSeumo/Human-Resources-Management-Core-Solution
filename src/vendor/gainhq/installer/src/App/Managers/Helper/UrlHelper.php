<?php


namespace Gainhq\Installer\App\Managers\Helper;


trait UrlHelper
{
    public function url($code = '', $type = 'verification')
    {
        $code = trim($code);
        $domain_name = request()->getHost();
        $config = config('gain');
        return "{$config['update_url']}/{$type}/{$config['app_id']}?domain_name={$domain_name}&purchase_key={$code}&app_version={$config['app_version']}";
    }
}
