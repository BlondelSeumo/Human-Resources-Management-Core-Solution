<?php

namespace App\Notifications;

use App\Notifications\Core\Helper\NotificationTemplateHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

abstract class BaseNotification extends Notification
{
    use Queueable;

    public $auth;

    public $templates;

    public $via;

    public $model;

    public $mailView;

    public $databaseNotificationUrl = null;

    public $mailSubject = null;

    public $databaseNotificationContent;

    public $nexmoNotificationContent;

    protected $tag;

    public function __construct()
    {
        $this->parseNotification();
    }

    public function via($notifiable)
    {
        return $this->via;
    }


    public function toMail($notifiable)
    {
        $template = $this->template()->mail();
        $content = $template->custom_content ?? $template->default_content;

        $tagParams = method_exists($this, 'tagParams') ? $this->tagParams($notifiable) : [$this->model, $this->auth, $notifiable];

        return (new MailMessage)
            ->view($this->mailView, [
                'template' => strtr(
                    $content,
                    class_exists($this->tag) ? (new $this->tag(...$tagParams))->notification() : []
                )
            ])
            ->subject($this->mailSubject);
    }

    public function toDatabase($notifiable)
    {
        return [
            "message" => $this->databaseNotificationContent,
            "name" => $notifiable->name,
            "url" => $this->databaseNotificationUrl,
            "notifier_id" => optional($this->auth)->id,
        ];

    }

    public function toNexmo($notifiable)
    {
        /*return (new NexmoMessage())
            ->content($this->nexmoNotificationContent);*/
    }

    public function template()
    {
        return new NotificationTemplateHelper($this->templates);
    }

    /**
     * This function must assign value to class variable which is needed to send your notification
     */
    abstract public function parseNotification();

}
