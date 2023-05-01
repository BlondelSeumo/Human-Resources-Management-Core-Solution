<?php

namespace Database\Seeders\App;

use App\Models\Core\Auth\Permission;
use App\Models\Core\Auth\Type;
use Database\Seeders\App\Traits\Permissions;
use Database\Seeders\Traits\DisableForeignKeys;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    use DisableForeignKeys, Permissions;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->disableForeignKeys();
        Permission::query()->truncate();

        $tenant = Type::findByAlias('tenant')->id;
        $appType = Type::findByAlias('app')->id;

        $common = [
            [
                'name' => 'invite_user',
                'type_id' => null,
                'group_name' => 'users'
            ],
            [
                'name' => 'cancel_user_invitation',
                'type_id' => null,
                'group_name' => 'users'
            ],
            [
                'name' => 'view_users',
                'type_id' => null,
                'group_name' => 'users'
            ],
            [
                'name' => 'update_users',
                'type_id' => null,
                'group_name' => 'users'
            ],
            [
                'name' => 'delete_users',
                'type_id' => null,
                'group_name' => 'users'
            ],
            [
                'name' => 'attach_roles_users',
                'type_id' => null,
                'group_name' => 'users'
            ],
            [
                'name' => 'detach_roles_users',
                'type_id' => null,
                'group_name' => 'users'
            ],
            [
                'name' => 'view_roles',
                'type_id' => null,
                'group_name' => 'roles'
            ],
            [
                'name' => 'create_roles',
                'type_id' => null,
                'group_name' => 'roles'
            ],
            [
                'name' => 'update_roles',
                'type_id' => null,
                'group_name' => 'roles'
            ],
            [
                'name' => 'delete_roles',
                'type_id' => null,
                'group_name' => 'roles'
            ],
            [
                'name' => 'attach_users_to_roles',
                'type_id' => null,
                'group_name' => 'roles'
            ],
//            [
//                'name' => 'view_custom_fields',
//                'type_id' => null,
//                'group_name' => 'custom_fields'
//            ],
//            [
//                'name' => 'create_custom_fields',
//                'type_id' => null,
//                'group_name' => 'custom_fields'
//            ],
//            [
//                'name' => 'update_custom_fields',
//                'type_id' => null,
//                'group_name' => 'custom_fields'
//            ],
//            [
//                'name' => 'delete_custom_fields',
//                'type_id' => null,
//                'group_name' => 'custom_fields'
//            ],
            [
                'name' => 'view_settings',
                'type_id' => null,
                'group_name' => 'settings'
            ],
            [
                'name' => 'update_settings',
                'type_id' => null,
                'group_name' => 'settings'
            ],
            [
                'name' => 'view_notification_settings',
                'type_id' => null,
                'group_name' => 'notification'
            ],
            [
                'name' => 'update_notification_settings',
                'type_id' => null,
                'group_name' => 'notification'
            ],
        ];

        $app = [
            [
                'name' => 'view_notification_templates',
                'type_id' => $appType,
                'group_name' => 'notification'
            ],
            [
                'name' => 'update_notification_templates',
                'type_id' => $appType,
                'group_name' => 'notification'
            ],

            [
                'name' => 'update_delivery_settings',
                'type_id' => $appType,
                'group_name' => 'settings'
            ],
            [
                'name' => 'view_delivery_settings',
                'type_id' => $appType,
                'group_name' => 'settings'
            ],
            [
                'name' => 'check_for_updates',
                'type_id' => $appType,
                'group_name' => 'update'
            ],
            [
                'name' => 'update_app',
                'type_id' => $appType,
                'group_name' => 'update'
            ],
        ];

        $permissions = array_merge($common, $app);

        $before = method_exists($this, 'beforeApplication') ? $this->beforeApplication($appType) : [];

        $tenantPermissions = method_exists($this, 'permissions') ? $this->permissions($tenant, $appType) : [];

        $this->enableForeignKeys();
        Permission::query()->insert(
            array_merge($before, array_merge(
                $permissions,
                $tenantPermissions
            ))
        );
    }
}
