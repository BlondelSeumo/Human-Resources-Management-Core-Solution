<?php

namespace App\Http\Controllers\Core\Notification;

use App\Filters\Core\BaseFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Core\Notification\NotificationTemplateRequest as Request;
use App\Models\Core\Notification\NotificationTemplate;
use App\Notifications\Core\Settings\SettingsNotification;
use App\Services\Core\Setting\NotificationTemplateService;
use Exception;

class NotificationTemplateController extends Controller
{
    public function __construct(NotificationTemplateService $service, BaseFilter $filter)
    {
        $this->filter = $filter;
        $this->service = $service;
    }

    /**
     * @return mixed
     */
    public function index()
    {
        return $this->service
            ->filters($this->filter)
            ->paginate(
                request('per_page', 15)
            );
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
        $template = $this->service->save();

        return created_responses('notification_template');
    }

    public function show(NotificationTemplate $notificationTemplate)
    {
        return $notificationTemplate;
    }

    /**
     * @param NotificationTemplate $notificationTemplate
     * @param Request $request
     * @return mixed
     */
    public function update(NotificationTemplate $notificationTemplate, Request $request)
    {
        $notificationTemplate->update(request()->all());

        notify()
            ->on('settings_updated')
            ->with(trans('default.notification_template'))
            ->send(SettingsNotification::class);

        return updated_responses('notification_template');
    }


    /**
     * @param NotificationTemplate $notificationTemplate
     * @return mixed
     * @throws Exception
     */
    public function destroy(NotificationTemplate $notificationTemplate)
    {
        if ($notificationTemplate->delete()) {
            return deleted_responses('notification_template');
        }
        return failed_responses();
    }
}
