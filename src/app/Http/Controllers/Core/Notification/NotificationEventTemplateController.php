<?php

namespace App\Http\Controllers\Core\Notification;

use App\Http\Controllers\Controller;
use App\Http\Requests\Core\Notification\NotificationEventTemplateRequest as Request;
use App\Models\Core\Setting\NotificationEvent;
use App\Services\Core\Setting\NotificationTemplateService;

class NotificationEventTemplateController extends Controller
{
    public function __construct(NotificationTemplateService $service)
    {
        $this->service = $service;
    }

    /**
     * @param NotificationEvent $notificationEvent
     * @param Request $request
     * @return mixed
     */
    public function store(NotificationEvent $notificationEvent, Request $request)
    {
        $templates = $this->service
            ->attachTemplates($notificationEvent);

        return attached_response('notification_template', ['templates' => $templates]);
    }


    /**
     * @param NotificationEvent $notificationEvent
     * @param Request $request
     * @return mixed
     */
    public function update(NotificationEvent $notificationEvent, Request $request)
    {
        $templates = $this->service
            ->detachTemplates($notificationEvent);

        return detached_response('notification_template', ['templates' => $templates]);
    }
}
