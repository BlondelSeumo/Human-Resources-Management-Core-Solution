<?php

return [
    /*
     |
     | Default tenant id
     |
     */
    'id' => 1,

    /*
     |
     | Default tenant name
     |
     */
     'name' => 'Default Tenant',

    /*
    |
    | This is a secret config if you change it could break your application
    | changeable
    |
    */
    'type' => env('APP_TYPE', 'single-tenant'),




];
