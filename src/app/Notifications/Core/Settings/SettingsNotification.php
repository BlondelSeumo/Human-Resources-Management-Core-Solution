<?php

namespace App\Notifications\Core\Settings;

use App\Mail\Tag\SettingTag;
use App\Notifications\BaseNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class SettingsNotification extends BaseNotification implements ShouldQueue
{
    use Queueable;

    public function __construct($templates, $via, $label = '')
    {
        $this->templates = $templates;
        $this->via = $via;
        $this->model = $label;
        $this->auth = auth()->user();
        $this->tag = SettingTag::class;
        parent::__construct();
    }

    public function parseNotification()
    {
        $this->mailView = 'notification.mail.settings.index';
        $this->databaseNotificationUrl = config('notification.settings_front_end_route_name') ? route(config('notification.settings_front_end_route_name')) : '';

        $this->mailSubject = optional($this->template()->mail())->parseSubject([
            '{name}' => $this->model
        ]);

        $this->databaseNotificationContent = optional($this->template()->database())->parse([
            '{name}' => $this->model
        ]);

        $this->nexmoNotificationContent = optional($this->template()->sms())->parse([
            '{name}' => $this->model
        ]);

    }
}
