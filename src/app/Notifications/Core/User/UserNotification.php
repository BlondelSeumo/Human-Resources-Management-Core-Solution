<?php

namespace App\Notifications\Core\User;

use App\Mail\Tag\UserTag;
use App\Notifications\BaseNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserNotification extends BaseNotification implements ShouldQueue
{
    use Queueable;

    public function __construct($templates, $via, $user)
    {
        $this->templates = $templates;
        $this->via = $via;
        $this->model = $user;
        $this->auth = auth()->user();
        $this->tag = UserTag::class;
        parent::__construct();
    }

    public function parseNotification()
    {
        $this->mailView = 'notification.mail.user.user_template';
        $this->databaseNotificationUrl = route(config('notification.user_front_end_route_name'), [
            'user' => $this->model->id
        ]);

        $this->mailSubject = optional($this->template()->mail())->parseSubject([
            '{name}' => $this->model->full_name
        ]);

        $this->databaseNotificationContent = optional($this->template()->database())->parse([
            '{name}' => $this->model->full_name
        ]);

        $this->nexmoNotificationContent = optional($this->template()->sms())->parse([
            '{name}' => $this->model->full_name
        ]);

    }

}
