<?php

namespace Database\Seeders;

use App\Models\Core\Auth\Role;
use App\Models\Core\Auth\Type;
use App\Models\Core\Notification\NotificationTemplate;
use App\Models\Core\Setting\NotificationAudience;
use App\Models\Core\Setting\NotificationEvent;
use App\Models\Core\Setting\NotificationSetting;
use Database\Seeders\Traits\DisableForeignKeys;
use Database\Seeders\Traits\TruncateTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class NewNotificationSeeder extends Seeder
{
    use TruncateTable, DisableForeignKeys;

    /**
     * Seed the application's database.
     */
    public function run()
    {
        Artisan::call('cache:clear');
        Model::unguard();
        activity()->withoutLogs(function () {
            $this->disableForeignKeys();

            $event = NotificationEvent::query()->create([
                'name' => 'user_create',
                'type_id' => 2,
            ]);

            $notification_setting = NotificationSetting::query()->create([
                'notification_event_id' => $event->id,
                'notify_by' =>  ['database', 'mail'],
                'tenant_id' => 1
            ]);

            $roles = Role::query()
                ->whereIn('alias', ['admin', 'manager'])
                ->whereHas('type', function (Builder $query) {
                    $query->where('alias', 'app');
                })->get()
                ->pluck('id');

            $notification_setting->audiences()->saveMany([
                new NotificationAudience([
                    'audience_type' => 'roles',
                    'audiences' => $roles
                ])
            ]);

            $template = [
                'system' => '',
                'subject' => 'Account has been created form {company_name}',
                'content' => '<p><img src="{company_logo}" style="height: 75px"></p>
<p>
</p><p><span style="background-color: var(--form-control-bg) ; color: var(--default-font-color) ;">Hi {receiver_name}</span><br></p><p>Hope this mail finds you well and healthy. You have been added to our company as an employee.
<p>Your Login credentials are below, </p> 
<p>Email : {email} </p> 
<p>Password : {password}</p>
<br>
<p>Please use these credentials to login into your account.</p><br>
<p><a href="{resource_url}" style="background: #4466F2;color: white;padding: 9px;border-radius: 4px;cursor: pointer; text-decoration: none; text-underline: none" target="_blank">Go to your account</a></p><br>
<p>You can change your password from your account password settings.</p>
Hope you will find useful!
<p></p><p>Thanks &amp; Regards,
</p><p>{company_name}</p>',
            ];

           $mail = NotificationTemplate::query()->create([
                'subject' => $template['subject'],
                'default_content' => $template['content'],
                'custom_content' => null,
                'type' => 'mail'
            ]);

            $event->templates()->attach(
                [$mail->id ]
            );

            $this->enableForeignKeys();
        });
        Model::reguard();
    }
}
