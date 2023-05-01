<?php


namespace App\Helpers\Core\General;


use App\Helpers\Notification\NotificationEventPicker;
use App\Models\Core\Auth\User;
use App\Models\Core\Setting\NotificationEvent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Notification;

class NotificationHelper
{
    protected $notificationAudiences = [];

    protected $notificationTemplates = [];

    protected $via = [];

    protected $data = [];

    protected $notificationPicker;

    public function __construct()
    {
        $this->notificationPicker = new NotificationEventPicker();
    }

    public function on(string $event)
    {
        if (config('notification.default_audiences')) {
            $notificationEvent = NotificationEvent::with('settings', 'templates')
                ->where('name', $event)
                ->first();

            $this->notificationAudiences = optional(optional($notificationEvent)->settings)->users();

            $this->notificationTemplates = optional($notificationEvent)->templates;

            $this->via = optional(optional($notificationEvent)->settings)->notifyBy;
            return  $this;
        }

        ['audiences' => $audiences, 'templates' => $templates, 'via' => $via] = $this->notificationPicker->on($event);

        $this->notificationAudiences = $audiences;

        $this->notificationTemplates = $templates;

        $this->via = $via;

        return $this;

    }

    public function via($channels)
    {
        $this->via = is_array($channels) ? $channels : func_get_args();
        return $this;
    }

    public function with($body)
    {
        $this->data = is_array($body) ? $body : func_get_args();
        return $this;
    }

    public function audiences($audiences)
    {
        $this->notificationAudiences = is_array($audiences) ? $audiences : func_get_args();
        return $this;
    }

    public function mergeAudiences($audiences)
    {
        $newAudience = is_array($audiences) ? $audiences : func_get_args();
        $this->notificationAudiences = array_unique(array_merge($this->notificationAudiences, $newAudience));
        return $this;
    }

    public function templates($templates)
    {
        $this->notificationTemplates = $templates;
        return $this;
    }

    public function send($notification)
    {
        $via = [];
        if ($this->via && count($this->via)) {
            $via = array_map(function ($via) {
                if ($via == 'sms')
                    $via = 'nexmo';
                return $via;
            }, $this->via);
        }

        if (class_exists($notification) && $this->notificationAudiences && count($this->notificationAudiences) && $this->notificationTemplates && count($this->notificationTemplates)){
            $notification = new $notification($this->notificationTemplates, $via, ...$this->data);

            $users = $this->notificationAudiences;

            if (!$this->notificationAudiences instanceof Collection) {
                $users = User::active()->find($this->getAudiences());
            }
            Notification::send($users, $notification);
        }

        return $this;
    }

    public function __get($name)
    {
        if ($name = 'audiences')
            return $this->notificationAudiences;
        else if ($name == 'templates')
            return $this->notificationTemplates;

        throw new \Exception('Trying to get property of non object '.$name);
    }

    protected function getAudiences() {
        if(config('notification.notify_notifier'))
            return $this->notificationAudiences;

        return array_filter($this->notificationAudiences, function ($audience) {
           return $audience != auth()->id();
        });
    }

}
