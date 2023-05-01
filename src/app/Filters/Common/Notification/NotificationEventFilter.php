<?php


namespace App\Filters\Common\Notification;


use App\Filters\Common\FilterContact;
use App\Helpers\Core\Traits\InstanceCreator;
use App\Models\Core\Setting\NotificationEvent;
use Illuminate\Database\Eloquent\Builder;

class NotificationEventFilter extends FilterContact
{
    use InstanceCreator;

    function filter(): Builder
    {
        return $this->query->with([
            'templates:id,subject,type',
            'settings' => function($query) {
                $query->when(optional(tenant())->id, function (Builder $builder) {
                    $builder->where('tenant_id', tenant()->id);
                }, function (Builder $builder) {
                    $builder->whereNull('tenant_id');
                });
            },
            'settings.audiences'
        ])->when(optional(tenant())->id, function (Builder $builder) {
            $builder->whereHas('type', function (Builder $builder) {
                $builder->where('alias', 'tenant');
            });
        }, function (Builder $builder) {
            $builder->whereHas('type', function (Builder $builder) {
                $builder->where('alias', 'app');
            });
        });
    }

    public function show(NotificationEvent $notificationEvent)
    {
        return $notificationEvent->load('templates', 'settings.audiences');
    }

}
