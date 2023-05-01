<?php

if (! function_exists('home_route')) {
    /**
     * Return the route to the "home" page depending on authentication/authorization status.
     *
     * @return array
     */
    function home_route()
    {
        if (auth()->check()) {
            return [
                'route_name' => 'tenant.dashboard',
                'route_params' => null
            ];
        }
        return [
            'route_name' => 'users.login.index',
            'route_params' => null
        ];
    }
}
