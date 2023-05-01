<?php
namespace Database\Seeders\Auth;

use App\Models\Core\Auth\Role;
use App\Models\Core\Auth\Type;
use App\Models\Core\Auth\User;
use Database\Seeders\Traits\DisableForeignKeys;
use Illuminate\Database\Seeder;

/**
 * Class PermissionRoleTableSeeder.
 */
class PermissionRoleTableSeeder extends Seeder
{
    use DisableForeignKeys;

    /**
     * Run the database seed.
     */
    public function run()
    {
        $this->disableForeignKeys();
        Role::query()->truncate();
        // Create Roles
        $superAdmin = User::first();

        $roles = [
            [
                'name' => config('access.users.app_admin_role'),
                'is_admin' => 1,
                'alias' => 'admin',
                'type_id' => Type::findByAlias('app')->id,
                'created_by' => $superAdmin->id,
                'is_default' => 1,
                'tenant_id' => 1
            ]
        ];

        Role::query()->insert($roles);

        $this->enableForeignKeys();
    }
}
