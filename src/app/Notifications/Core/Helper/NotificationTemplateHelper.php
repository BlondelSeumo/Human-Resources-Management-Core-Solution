<?php


namespace App\Notifications\Core\Helper;


use App\Helpers\Core\Traits\InstanceCreator;
use App\Models\Core\Notification\NotificationTemplate;
use App\Models\Core\Setting\NotificationEvent;

class NotificationTemplateHelper
{
    use InstanceCreator;

    protected $templates;

    public $template;

    public $event;

    public function __construct($templates = [])
    {
        $this->templates = $templates;
    }

    public function sms()
    {
        return $this->finder('sms');
    }

    public function mail()
    {
        return $this->finder();
    }

    public function database()
    {
        return $this->finder('database');
    }


    /**
     * @param string $template
     * @return NotificationTemplate
     */
    protected function finder($template = 'mail')
    {
        return $this->template = collect($this->getTemplates())->first(function ($t) use ($template) {
            return $t->type == $template;
        });
    }

    public function parse(array $vars = [], $template = null)
    {
        $template_string = $template = $template ?? $this->template;
        if (!is_string($template))
            $template_string = $template->custom_content ?? $template->default_content;

        return strtr($template_string, $vars);
    }

    public function on(string $event)
    {
        $this->event = $event;

        return $this;
    }

    public function getTemplates()
    {
        if (count($this->templates)) {
            return $this->templates;
        }

        $event = NotificationEvent::with('templates')
            ->where('name', $this->event)
            ->first(['id', 'name']);

        return $event->templates;
    }
}
