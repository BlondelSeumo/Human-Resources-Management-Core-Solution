<?php
namespace Database\Seeders\Auth;

use App\Models\Core\Auth\User;
use Database\Seeders\Traits\DisableForeignKeys;
use Illuminate\Database\Seeder;

/**
 * Class UserRoleTableSeeder.
 */
class UserRoleTableSeeder extends Seeder
{
    use DisableForeignKeys;

    /**
     * Run the database seed.
     */
    public function run()
    {
        $this->disableForeignKeys();

        $user = User::query()->find(1);
        $user->assignRole(config('access.users.app_admin_role'));

        $this->enableForeignKeys();
    }
}
