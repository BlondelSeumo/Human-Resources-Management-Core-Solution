# Installer Package

## Integration


#### Clone this repository 

1. ```Link will be provided``` 


#### Setup directory 

App level modification: 

1. Need to create a new directory on root of your project.
2. Add a folder named package.
3. Inside package folder add another folder named installer.
4. It should look like: ```package/installer```
5. Copy these files only 'composer.json' , 'composer.lock', 'package-lock.json', 
'package.json' , 'src' , 'webpack.mix.js' and paste in ```package/installer``` from 
```git@github.com:GainHQ/Package.Installer.git```
 repository which is cloned.

 
#### Composer json

1. Modify Composer json of you project.
2. Add  ```"gainhq/installer": "*"``` in ```require``` section.
3. Add new ```repositories``` section in the composer.json.
```
"repositories": [
        {
            "type": "path",
            "url":  "./package/installer",
            "options": {
                "symlink": false
            }
        }
    ]
```
#### Kernel.php
Add in protected $routeMiddleware
```
'valid_purchase_code' => \Gainhq\Installer\App\Middleware\ValidPurchaseCodeMiddleware::class
```

#### Seeder
In config/installer.php modify your seed as per your requirement.

```
'seeder' =>[
        'installer_admin_seeder' => '\Database\Seeders\Auth\PermissionRoleTableSeeder',
        'setup_seeder' => '\Database\Seeders\SetupSeeder',
        'status_seeder' => '\Database\Seeders\Status\StatusSeeder',
        'type_seeder' => '\Database\Seeders\Auth\TypeSeeder',
    ]
```
 
#### Link language file
1. In lang/en there will be a setup.php lang
2. In set.php in custom.php
3. At the end of custom.php use array_merge to include setup.php. Exp: array_merge(include 'CRM/permissions.php', include 'setup.php') 

#### Test Locally
1. run ```php artisan serve```
2. ```composer i```
3. ```php artisan vendor:publish --tag=gainhq-installer --force```


#### Modify deploy yml

1. Go to .github/workflows to modify yml files. 
2. Need to modify both dev-deploy.yml and test-deploy.yml file. 
3. Add Installer vendor publish section after the composer install section.
```
- name: Install Composer dependencies 🤞
  run: |
      rm composer.lock
      composer install

- name: Installer vendor publish 🤞
  run: |
      php artisan vendor:publish --tag=gainhq-installer --force
```

#### .env.cli

Make sure .env.cli in correct

```
APP_READ_ONLY=false
APP_NAME="Jobpoint"
APP_ENV=production
APP_KEY=
APP_URL=
APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_LOCALE_PHP=en_US
APP_TIMEZONE=UTC
LOG_CHANNEL=daily
DEBUGGER_ENABLED=false
SINGLE_LOGIN=false
APP_DEBUG=false
APP_INSTALLED=true

#Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=homestead
DB_USERNAME=homestead
DB_PASSWORD=secret

#Session and Broadcast
BROADCAST_DRIVER=log
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=cookie
SESSION_LIFETIME=120
SESSION_ENCRYPT=false

#Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

#Mail
MAIL_DRIVER=smtp
MAIL_HOST=localhost
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=hello@example.com
MAIL_FROM_NAME="${APP_NAME}"

#AWS
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=

#Pusher
PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_APP_CLUSTER=mt1
MIX_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
MIX_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"

# Access
ENABLE_REGISTRATION=true
CHANGE_EMAIL=false
PASSWORD_HISTORY=3
PASSWORD_EXPIRES_DAYS=30

# This should be one or the other, or neither
REQUIRES_APPROVAL=false
CONFIRM_EMAIL=true
#//

# Get your credentials at: https://www.google.com/recaptcha/admin
CONTACT_CAPTCHA_STATUS=false
REGISTRATION_CAPTCHA_STATUS=false
LOGIN_CAPTCHA_STATUS=false

#Recaptcha
INVISIBLE_RECAPTCHA_SITEKEY=
INVISIBLE_RECAPTCHA_SECRETKEY=
INVISIBLE_RECAPTCHA_BADGEHIDE=false
INVISIBLE_RECAPTCHA_DATABADGE='bottomright'
INVISIBLE_RECAPTCHA_TIMEOUT=5
INVISIBLE_RECAPTCHA_DEBUG=false

# google reCaptcha v2 credentials
CAPTCHA_KEY=
CAPTCHA_SECRET=

# Socialite Providers
FACEBOOK_ACTIVE=false
#FACEBOOK_CLIENT_ID=
#FACEBOOK_CLIENT_SECRET=
#FACEBOOK_REDIRECT=${APP_URL}/login/facebook/callback

BITBUCKET_ACTIVE=false
#BITBUCKET_CLIENT_ID=
#BITBUCKET_CLIENT_SECRET=
#BITBUCKET_REDIRECT=${APP_URL}/login/bitbucket/callback

GITHUB_ACTIVE=false
#GITHUB_CLIENT_ID=
#GITHUB_CLIENT_SECRET=
#GITHUB_REDIRECT=${APP_URL}/login/github/callback

GOOGLE_ACTIVE=false
#GOOGLE_CLIENT_ID=
#GOOGLE_CLIENT_SECRET=
#GOOGLE_REDIRECT=${APP_URL}/login/google/callback

LINKEDIN_ACTIVE=false
#LINKEDIN_CLIENT_ID=
#LINKEDIN_CLIENT_SECRET=
#LINKEDIN_REDIRECT=${APP_URL}/login/linkedin/callback

TWITTER_ACTIVE=false
#TWITTER_CLIENT_ID=
#TWITTER_CLIENT_SECRET=
#TWITTER_REDIRECT=${APP_URL}/login/twitter/callback

#Instalation
INSTALL_DEMO_DATA=true
DEPLOYMENT_KEY=
PREPARE_RELEASE_FILES=true
RESTRUCTURE=true
IS_DEV=true
```


#### Config file changes
1. config/gain.php (modify)
2. config/installer.php (add)

#### gain.php
1. add 'use_update_route' => 'v2' in gain.php
```
<?php
return [
    /* Application version */
    'app_version' => '1.1',

    /* Application update url */
    'update_url' => 'https://marketplace.gainhq.com',

    /* Application id */
    'app_id' => 'jobpoint',

    /*Determine if the app is Installed or not*/
    'installed' => env('APP_INSTALLED', true),

    'use_update_route' => 'v2',

];

```

#### installer.php

```
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
		'symlink_requirement' => ''
	],
    'manual_update' => false
];

```

#### Update

If any update is in package occurs then need to follow the steps:
1. pull the master of GainHQ /Package.Installer
2. copy the content to your app package/installer
3. delete composer.lock.
4. run ```composer i```
5. run ```php artisan vendor:publish --tag=gainhq-installer --force```
6. test in you local server for changes

To replace documentation links you need to back up config files. 
1. Make sure to back up package/installer/src/install/config.json
2. Make sure to back up package/installer/src/config/installer.php