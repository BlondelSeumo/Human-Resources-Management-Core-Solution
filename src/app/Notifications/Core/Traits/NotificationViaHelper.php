<?php


namespace App\Notifications\Core\Traits;


use Illuminate\Support\Str;

trait NotificationViaHelper
{
    protected $is_mail_settings_available = false;

    public function vias(array $via = null)
    {
        if ($via && count($via)) {
            $method = 'whenItIs'.Str::studly(config('mail.driver'));
            if (method_exists($this, $method)) {
                $this->is_mail_settings_available = $this->$method();
            }

            return collect($via)->filter(function ($via) {
                    if ($via == 'mail') {
                        return $this->is_mail_settings_available;
                    }
                    return true;
            })->toArray();
        }
        return [];
    }

    public function whenItIsSmtp()
    {
        return !empty(config('mail.password'));
    }

    public function whenItIsSendmail()
    {
        return true;
    }

    public function whenItIsMailgun()
    {
        return !empty(config('services.mailgun.secret'));
    }

    public function whenItIsMandrill()
    {
        return !empty(config('services.mandrill.secret'));
    }

    public function whenItIsSes()
    {
        return !empty(config('services.ses.secret'));
    }

    public function whenItIsSparkpost()
    {
        return !empty(config('services.sparkpost.secret'));
    }

    public function whenItIsPostmark()
    {
        return !empty(config('services.postmark.token'));
    }

    public function whenItIsLog()
    {
        return true;
    }

    public function whenItIsArray()
    {
        return true;
    }

}
