<?php

namespace App\Http\Controllers\Core\Notification;

use App\Http\Controllers\Controller;
use App\Services\Core\Auth\UserService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $request = request();
        $notifications = auth()->user()->notifications();

        if ($request->read)
            $notifications = $notifications->whereNotNull( 'read_at' );
        if ($request->unread)
            $notifications = $notifications->whereNull( 'read_at' );

        $notifications = $notifications->latest( 'created_at' );

        $date = json_decode(htmlspecialchars_decode($request->get('date')), true);

        $notifications = $notifications->when($date, function (Builder $builder) use ($date) {
            $builder->whereBetween(\DB::raw('DATE(created_at)'), array_values($date));
        })->when($request->get('search'), function (Builder $builder)use ($request) {
            $builder->whereRaw('JSON_UNQUOTE(JSON_EXTRACT(data, "$.message")) LIKE ?', ["%".$request->get('search')."%"]);
        })->paginate($request->get('per_page', 10));

        $notifications->each(function (DatabaseNotification $notification) {
            $notification->setRelation(
                'notifier',
                isset($notification->data['notifier_id']) ?
                    $this->service
                    ->findAndCacheUser($notification->data['notifier_id'])
                    : null
            );
        });

        return $notifications;

    }

    public function markAsRead($id)
    {
        $notification = auth()->user()->notifications()->whereId( $id )->first();
        $notification->markAsRead();
        return $notification;
    }

    public function markAsUnread($id)
    {
        $notification = auth()->user()->notifications()->whereId( $id )->first();
        $notification->markAsUnread();
        return $notification;

    }

    public function markAsReadAll()
    {
        auth()->user()->unreadNotifications->markAsRead();
        return response()->json(['status' => true, 'message' => __t('notification_has_cleared_from_list')]);
    }
}
