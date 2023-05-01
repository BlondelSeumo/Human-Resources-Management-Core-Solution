<?php


namespace App\Services\Core\Setting;

use App\Helpers\Core\Traits\Helpers;
use App\Models\Core\Notification\NotificationTemplate;
use App\Models\Core\Setting\NotificationEvent;
use App\Services\Core\BaseService;

class NotificationTemplateService extends BaseService
{
    use Helpers;
    public function __construct(NotificationTemplate $template)
    {
        $this->model = $template;
    }

    /**
     * @param NotificationEvent $event
     * @return NotificationEvent
     */
    public function attachTemplates(NotificationEvent $event)
    {
        $templates = $this->checkMakeArray(
            request('templates')
        );

        foreach ($templates as $template) {
            if (!$event->templates->contains($template)) {
                $notification_template = $this->model::query()->find($template);
                $event_template = $event->templates()->where('type', $notification_template->type)->first();
                if ($event_template) {
                    $event->templates()->detach($event_template->id);
                }
                $event->templates()->attach($template);
            }
        }

        return $event->load('templates');
    }


    /**
     * @param NotificationEvent $event
     * @return NotificationEvent
     */
    public function detachTemplates(NotificationEvent $event)
    {
        $templates = $this->checkMakeArray(
            request('templates')
        );

        $event->templates()->detach($templates);

        return $event->load('templates');
    }
}
