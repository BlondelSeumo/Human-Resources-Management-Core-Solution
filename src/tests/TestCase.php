<?php

namespace Tests;

use App\Models\Core\Auth\Role;
use App\Models\Core\Auth\Type;
use App\Models\Core\Auth\User;
use App\Models\Core\Status;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

/**
 * Class TestCase.
 */
abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase;

    /**
     * Create the admin role or return it if it already exists.
     *
     * @return mixed
     */
    protected function getAdminRole()
    {
        if ($role = Role::whereName(config('access.users.admin_role'))->first()) {
            return $role;
        }

        Type::create([
            "name" => "App",
            "alias" => "app"
        ]);

        $adminRole = factory(Role::class)
            ->create([
                'name' => 'Admin',
                'is_default' => 1,
                'is_admin' => '1'
            ]);

        return $adminRole;
    }

    /**
     * Create an administrator.
     *
     * @param array $attributes
     *
     * @return mixed
     */
    protected function createAdmin(array $attributes = [])
    {
        $adminRole = $this->getAdminRole();
        Status::create([
            'name' => 'status_active',
            'type' => 'user',
            'class' => 'success'
        ]);

        $admin = factory(User::class)->create($attributes);
        $admin->assignRole($adminRole);

        return $admin;
    }

    /**
     * Login the given administrator or create the first if none supplied.
     *
     * @param bool $admin
     *
     * @return bool|mixed
     */
    protected function loginAsAdmin($admin = false)
    {
        if (! $admin) {
            $admin = $this->createAdmin();
        }

        $this->actingAs($admin);

        return $admin;
    }
}
