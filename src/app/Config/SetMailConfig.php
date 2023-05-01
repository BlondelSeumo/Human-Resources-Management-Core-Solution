<?php


namespace App\Config;


use App\Helpers\Core\Traits\InstanceCreator;
use App\Services\Settings\SettingService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class SetMailConfig
{
    use InstanceCreator;

    protected $settings;

    public function __construct($settings = [])
    {
        $this->settings = $settings;
    }

    public function clear()
    {
        Artisan::call('config:clear');
        return $this;
    }

    public function set()
    {
        $settings = $this->getSettings();

        if (!empty($settings['provider'])) {
            $method = 'set' . Str::studly($settings['provider']);
            if (method_exists($this, $method)) {
                $this->{$method}($settings);
            }
        }
        return true;
    }

    /**
     * @return array
     */
    public function getSettings()
    {
        if (!count($this->settings)) {
            return resolve(SettingService::class)->getCachedMailSettings();
        }

        return $this->settings;
    }

    public function setEmailName(array $settings)
    {
        config()->set('mail.from.address', $settings['from_email']);
        config()->set('mail.from.name', $settings['from_name']);
    }

    public function setMailgun(array $settings)
    {
        $this->setEmailName($settings);
        config()->set('mail.driver', 'mailgun');
        config()->set('services.mailgun.domain', $settings['domain_name']);
        config()->set('services.mailgun.secret', $settings['api_key']);
    }

    public function setAmazonSes(array $settings)
    {
        $this->setEmailName($settings);
        config()->set('mail.driver', 'ses');
        config()->set('services.ses.key', $settings['access_key_id']);
        config()->set('services.ses.secret', $settings['secret_access_key']);
        config()->set('services.ses.region', $settings['api_region']);
        config()->set('services.ses.configuration_set', isset($settings['configuration_set']) ? $settings['configuration_set'] : '');
    }

    public function setSmtp(array $settings)
    {
        $this->setEmailName($settings);
        config()->set('mail.driver', 'smtp');
        config()->set('mail.host', $settings['smtp_host']);
        config()->set('mail.port', $settings['smtp_port']);
        config()->set('mail.encryption', $settings['smtp_encryption']);
        config()->set('mail.username', $settings['smtp_user_name']);
        config()->set('mail.password', $settings['smtp_password']);
    }

    public function setMandrill(array $settings)
    {
        $this->setEmailName($settings);
        config()->set('mail.driver', 'mandrill');
        config()->set('services.mandrill.secret', $settings['mandrill_secret']);
    }

    public function setPostmark(array $settings)
    {
        $this->setEmailName($settings);
        config()->set('mail.driver', 'postmark');
        config()->set('services.postmark.token', $settings['postmark_token']);
    }

    public function setSparkpost(array $settings)
    {
        $this->setEmailName($settings);
        config()->set('mail.driver', 'sparkpost');
        config()->set('services.sparkpost.secret', $settings['sparkpost_secret']);
    }

    public function setSendmail(array $settings)
    {
        $this->setEmailName($settings);
        config()->set('mail.driver', 'sendmail');
        if (isset($settings['path'])) {
            config()->set('mail.sendmail', $settings['path']);
        }
    }
}
