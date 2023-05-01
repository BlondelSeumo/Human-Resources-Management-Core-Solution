<?php

return [
    /*
     * Not included at all in installer
     * Use boolean instead of string
     *
     *  Must: ('email_set_up' => true), Never use  ('email_set_up' => 'true')
     * */
    'include_option' => [
        'email_set_up' => false,
        'pusher_set_up' => false,
    ],

    /*
     * Included but can be skipped in installer
     * Use boolean instead of string
     *
     * enable include_option true but disable skip_option
     * This is for Showing or Hiding "Skip" Button
     *  Exp:
     * If we want to enable email_set_up option but say we don't want user to skip the option
        'include_option' => [
            'email_set_up' => true,
        ],
        'skip_option' => [
            'email_set_up' => false,
        ],
    */
    'skip_option' => [
        'email_set_up' => false,
        'pusher_set_up' => false,
    ],
    'core' => [
        /*Any changes in min_php_version and min_mysql_version need to update in public/install/config.json as well */
        'min_php_version' => '8.0',
        'min_mysql_version' => '5.6'
    ],
    'seeder' =>[
        'installer_admin_seeder' => '\Database\Seeders\Auth\PermissionRoleTableSeeder',
        'setup_seeder' => '\Database\Seeders\SetupSeeder',
        'status_seeder' => '\Database\Seeders\Status\StatusSeeder',
        'type_seeder' => '\Database\Seeders\Auth\TypeSeeder',
    ],
	
	'link' => [
        'symlink_requirement' => 'https://payday.gainhq.com/documentation/server-requirement.html#symlink_permission'
	],
    'manual_update' => false
];
