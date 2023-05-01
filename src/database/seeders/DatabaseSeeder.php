<?php

namespace Database\Seeders;

use Database\Seeders\App\NotificationChannelTableSeeder;
use Database\Seeders\App\NotificationEventTableSeeder;
use Database\Seeders\App\NotificationSettingsSeeder;
use Database\Seeders\App\PermissionSeeder;
use Database\Seeders\App\TenantNotificationTemplateSeeder;
use Database\Seeders\App\TenantSettingSeeder;
use Database\Seeders\Auth\PermissionRoleTableSeeder;
use Database\Seeders\Auth\TypeSeeder;
use Database\Seeders\Auth\UserRoleTableSeeder;
use Database\Seeders\Auth\UserTableSeeder;
use Database\Seeders\Builder\CustomFieldTypeSeeder;
use Database\Seeders\Status\StatusSeeder;
use Database\Seeders\Tenant\DepartmentSeeder;
use Database\Seeders\Tenant\DesignationSeeder;
use Database\Seeders\Tenant\EmploymentStatusSeeder;
use Database\Seeders\Tenant\LeaveCategorySeeder;
use Database\Seeders\Tenant\LeavePeriodSeeder;
use Database\Seeders\Tenant\RoleSeeder;
use Database\Seeders\Tenant\TenantNotificationEventSeeder;
use Database\Seeders\Tenant\UserJobDeskSeeder;
use Database\Seeders\Tenant\WorkingShiftSeeder;
use Database\Seeders\Traits\DisableForeignKeys;
use Database\Seeders\Traits\TruncateTable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
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

            $this->call(StatusSeeder::class);
            $this->call(TypeSeeder::class);
            $this->call(UserTableSeeder::class);
            $this->call(PermissionSeeder::class);
            $this->call(PermissionRoleTableSeeder::class);
            $this->call(UserRoleTableSeeder::class);
            $this->call(CustomFieldTypeSeeder::class);
            $this->call(NotificationChannelTableSeeder::class);
            $this->call(NotificationEventTableSeeder::class);
            $this->call(TenantNotificationEventSeeder::class);
            $this->call(NotificationSettingsSeeder::class);

            $this->call(TenantNotificationTemplateSeeder::class);
            $this->call(LeavePeriodSeeder::class);
            $this->call(LeaveCategorySeeder::class);

            $this->call(DepartmentSeeder::class);
            $this->call(DesignationSeeder::class);
            $this->call(EmploymentStatusSeeder::class);
            //$this->call(WorkingShiftSeeder::class);
            $this->call(RoleSeeder::class);

            $this->call(TenantSettingSeeder::class);
            $this->call(UserJobDeskSeeder::class);

            $this->enableForeignKeys();
        });
        Model::reguard();
    }
}
