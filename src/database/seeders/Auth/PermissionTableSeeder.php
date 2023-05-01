<?php
namespace Database\Seeders\Auth;

use App\Models\Core\Auth\Permission;
use App\Models\Core\Auth\Type;
use Database\Seeders\Traits\DisableForeignKeys;
use Illuminate\Database\Seeder;

class PermissionTableSeeder extends Seeder
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
        Permission::query()->truncate();
        $appId = Type::findByAlias('app')->id;

        $permissions = [
            //users
            [
                'name' => 'view_users',
                'type_id' => $appId,
                'group_name' => 'users'
            ],
            [
                'name' => 'manage_dashboard',
                'type_id' => $appId,
                'group_name' => 'dashboard'
            ],
            [
                'name' => 'create_users',
                'type_id' => $appId,
                'group_name' => 'users'
            ],
            [
                'name' => 'update_users',
                'type_id' => $appId,
                'group_name' => 'users'
            ],
            [
                'name' => 'delete_users',
                'type_id' => $appId,
                'group_name' => 'users'
            ],
            [
                'name' => 'cancel_user_invitation',
                'type_id' => $appId,
                'group_name' => 'users'
            ],
            [
                'name' => 'attach_roles_users',
                'type_id' => $appId,
                'group_name' => 'users'
            ],
            [
                'name' => 'detach_roles_users',
                'type_id' => $appId,
                'group_name' => 'users'
            ],
            //end users
            //roles
            [
                'name' => 'view_roles',
                'type_id' => $appId,
                'group_name' => 'roles'
            ],
            [
                'name' => 'create_roles',
                'type_id' => $appId,
                'group_name' => 'roles'
            ],
            [
                'name' => 'update_roles',
                'type_id' => $appId,
                'group_name' => 'roles'
            ],
            [
                'name' => 'delete_roles',
                'type_id' => $appId,
                'group_name' => 'roles'
            ],
            [
                'name' => 'attach_users_to_roles',
                'type_id' => $appId,
                'group_name' => 'roles'
            ],
//            [
//                'name' => 'view_custom_fields',
//                'type_id' => $appId,
//                'group_name' => 'custom_fields'
//            ],
//            [
//                'name' => 'create_custom_fields',
//                'type_id' => $appId,
//                'group_name' => 'custom_fields'
//            ],
//            [
//                'name' => 'update_custom_fields',
//                'type_id' => $appId,
//                'group_name' => 'custom_fields'
//            ],
//            [
//                'name' => 'delete_custom_fields',
//                'type_id' => $appId,
//                'group_name' => 'custom_fields'
//            ],
            [
                'name' => 'attach_permissions_roles',
                'type_id' => $appId,
                'group_name' => 'permissions'
            ],
            [
                'name' => 'detach_permissions_roles',
                'type_id' => $appId,
                'group_name' => 'permissions'
            ],
            //roles

            //settings
            [
                'name' => 'view_settings',
                'type_id' => $appId,
                'group_name' => 'settings'
            ],
            [
                'name' => 'update_settings',
                'type_id' => $appId,
                'group_name' => 'settings'
            ],
            [
                'name' => 'change_settings_users',
                'type_id' => $appId,
                'group_name' => 'users'
            ],
            [
                'name' => 'settings_list_users',
                'type_id' => $appId,
                'group_name' => 'users'
            ],
            /*[
                'name' => 'change_password_users',
                'type_id' => $appId,
                'group_name' => 'users'
            ],*/
          /*  [
                'name' => 'change_profile_picture_users',
                'type_id' => $appId,
                'group_name' => 'users'
            ],*/
            [
                'name' => 'update_delivery_settings',
                'type_id' => $appId,
                'group_name' => 'settings'
            ],
            [
                'name' => 'view_delivery_settings',
                'type_id' => $appId,
                'group_name' => 'settings'
            ],
            [
                'name' => 'view_brand_delivery_settings',
                'type_id' => $appId,
                'group_name' => 'settings'
            ],
            [
                'name' => 'update_brand_privacy_settings',
                'type_id' => $appId,
                'group_name' => 'settings'
            ],
            [
                'name' => 'view_brand_privacy_settings',
                'type_id' => $appId,
                'group_name' => 'settings'
            ],
            [
                'name' => 'view_notification_settings',
                'type_id' => $appId,
                'group_name' => 'settings'
            ],
            [
                'name' => 'update_notification_settings',
                'type_id' => $appId,
                'group_name' => 'settings'
            ],
            //end of settings

            //Notification Templates
            [
                'name' => 'view_notification_templates',
                'type_id' => $appId,
                'group_name' => 'templates'
            ],
            [
                'name' => 'create_notification_templates',
                'type_id' => $appId,
                'group_name' => 'templates'
            ],
            [
                'name' => 'update_notification_templates',
                'type_id' => $appId,
                'group_name' => 'templates'
            ],
            [
                'name' => 'delete_notification_templates',
                'type_id' => $appId,
                'group_name' => 'templates'
            ],
            [
                'name' => 'attach_templates_notification_events',
                'type_id' => $appId,
                'group_name' => 'notification_events'
            ],
            [
                'name' => 'detach_templates_notification_events',
                'type_id' => $appId,
                'group_name' => 'notification_events'
            ],
//            [
//                'name' => 'update_corn_job_settings',
//                'type_id' => $appId
//            ],
//            [
//                'name' => 'view_corn_job_settings',
//                'type_id' => $appId
//            ],
            [
                'name' => 'view_activity_logs',
                'type_id' => $appId,
                'group_name' => 'log'
            ],
            [
                'name' => 'view_templates',
                'type_id' => $appId,
                'group_name' => 'templates'
            ],
            [
                'name' => 'create_templates',
                'type_id' => $appId,
                'group_name' => 'templates'
            ],
            [
                'name' => 'update_templates',
                'type_id' => $appId,
                'group_name' => 'templates'
            ],
            [
                'name' => 'delete_templates',
                'type_id' => $appId,
                'group_name' => 'templates'
            ],
            [
                'name' => 'invite_user',
                'type_id' => $appId,
                'group_name' => 'users'
            ],
        ];

        $this->enableForeignKeys();
        Permission::query()->insert($permissions);
    }
}
