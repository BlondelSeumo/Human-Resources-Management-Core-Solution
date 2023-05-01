<?php
namespace Database\Seeders\App;

use App\Models\Core\Auth\Type;
use App\Models\Core\Setting\NotificationEvent;
use Database\Seeders\Traits\DisableForeignKeys;
use Illuminate\Database\Seeder;

class NotificationEventTableSeeder extends Seeder
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
        $tenant = Type::findByAlias('tenant')->id;
        $events = [
            [
                'name' => 'user_invitation',
                'type_id' => $tenant,
            ],
            [
                'name' => 'user_create',
                'type_id' => $tenant,
            ],
            [
                'name' => 'user_invitation_canceled',
                'type_id' => $tenant,
            ],
            [
                'name' => 'user_invited',
                'type_id' => $tenant,
            ],
            [
                'name' => 'user_joined',
                'type_id' => $tenant,
            ],
            [
                'name' => 'password_reset',
                'type_id' => $tenant,
            ],
        ];

        NotificationEvent::query()->truncate();
        NotificationEvent::query()->insert($events);
        $this->enableForeignKeys();
    }
}
