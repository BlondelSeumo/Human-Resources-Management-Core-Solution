<?php

namespace Gainhq\Installer\App\Managers;

use Gainhq\Installer\App\Exceptions\GeneralException;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;


class DatabaseManager
{
    /**
     * As some server doesn't grabs the env even after cleaning the config we have to set the database to config
     */
    public function setConfig()
    {
        config()->set('database.default', request()->database_connection);
        config()->set('database.connections.'.request()->database_connection.'.host', request()->database_hostname);
        config()->set('database.connections.'.request()->database_connection.'.port', request()->database_port);
        config()->set('database.connections.'.request()->database_connection.'.database', request()->database_name);
        config()->set('database.connections.'.request()->database_connection.'.username', request()->database_username);
        config()->set('database.connections.'.request()->database_connection.'.password', request()->database_password);

        DB::purge(request()->database_connection);

        DB::reconnect(request()->database_connection);

        try {
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            throw new GeneralException(trans('default.database_credential_error'));
        }

    }

    public function migrate()
    {
        Artisan::call('migrate:fresh', ['--force' => true]);

        return true;
    }

    public function seed()
    {
        Artisan::call('db:seed', ['--force' => true]);

        return true;
    }

    public function seedEssential()
    {
        Artisan::call('db:seed', ['--class' => config('installer.seeder.status_seeder'), '--force' => true]);

        Artisan::call('db:seed', ['--class' => config('installer.seeder.type_seeder'), '--force' => true]);
    }

    public function seedRole()
    {
        Artisan::call('db:seed', [
            '--class' => config('installer.seeder.installer_admin_seeder'),
            '--force' => true
        ]);
    }
}
