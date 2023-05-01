<?php

namespace Database\Seeders\App;

use App\Models\Core\Auth\Role;
use App\Models\Core\Setting\NotificationAudience;
use App\Models\Core\Setting\NotificationChannel;
use App\Models\Core\Setting\NotificationEvent;
use App\Models\Core\Setting\NotificationSetting;
use Database\Seeders\Traits\DisableForeignKeys;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Seeder;

class NotificationSettingsSeeder extends Seeder
{
    use DisableForeignKeys;
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        $this->disableForeignKeys();

        $channels = ['database'];

        if (!env('IS_DEMO', false)){
            array_push($channels, 'mail');
        }

        $roles = Role::query()
            ->whereIn('alias', ['admin', 'manager'])
            ->whereHas('type', function (Builder $query) {
                $query->where('alias', 'app');
            })->get()
            ->pluck('id');

        NotificationSetting::query()->truncate();

        NotificationEvent::all()->map(function ($event) use ($channels, $roles) {
            $notification_setting = NotificationSetting::query()->create([
                'notification_event_id' => $event->id,
                'notify_by' => $channels,
                'tenant_id' => 1
            ]);

            $notification_setting->audiences()->saveMany([
                new NotificationAudience([
                    'audience_type' => 'roles',
                    'audiences' => $roles
                ])
            ]);
        });
        $this->enableForeignKeys();
    }
}
