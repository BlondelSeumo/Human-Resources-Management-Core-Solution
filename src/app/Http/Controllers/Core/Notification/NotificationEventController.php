<?php

namespace App\Http\Controllers\Core\Notification;

use App\Filters\Common\Notification\NotificationEventFilter as AppNotificationEventFilter;
use App\Filters\Core\NotificationEventFilter;
use App\Http\Controllers\Controller;
use App\Models\Core\Setting\NotificationEvent;
use Illuminate\Database\Eloquent\Builder;

class NotificationEventController extends Controller
{
    public function __construct(NotificationEventFilter $filter)
    {
        $this->filter = $filter;
    }

    /**
     * @return mixed
     */
    public function index()
    {
        return AppNotificationEventFilter::new(
            true,
            NotificationEvent::select(['id', 'name', 'type_id'])
                ->filters($this->filter)
        )->filter()
            ->when(request()->search, function (Builder $builder) {
                $builder->where('name', 'LIKE', "%".request()->search."%");
            })
            ->paginate(request('per_page', 10));
    }

    public function show(NotificationEvent $notificationEvent)
    {
        return AppNotificationEventFilter::new()->show(
            $notificationEvent
        );
    }
}
