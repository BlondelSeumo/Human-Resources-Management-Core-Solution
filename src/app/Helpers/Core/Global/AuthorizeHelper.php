<?php

use App\Helpers\Core\General\AuthorizeHelper;
use Illuminate\Auth\Access\AuthorizationException;

if (!function_exists('authorize')) {

    /**
     * @param $actions array|string
     * @return bool
     * @throws AuthorizationException
     * Authorize an action for the user. It could be multiple
     */
    function authorize($actions)
    {
        return resolve(AuthorizeHelper::class)->authorize($actions);
    }
}

if (!function_exists('get_authorized')) {

    /**
     * @param string $action
     * @return mixed
     * get authorized list if empty that mean whole list
     */
    function get_authorized(string $action)
    {
        return resolve(AuthorizeHelper::class)->getAuthorized($action);
    }
}

if (!function_exists('is_authorized_specific')) {

    /**
     * @param $action
     * @param $payload string|integer
     * @return mixed
     * is a specific model is authorized
     * @throws AuthorizationException
     */
    function is_authorized_specific(string $action, $payload)
    {
        return resolve(AuthorizeHelper::class)->isAuthorizedSpecific($action, $payload);
    }
}


if (!function_exists('authorize_any')) {

    /**
     * @param array $actions
     * @return boolean
     */
    function authorize_any(array $actions)
    {
        return resolve(AuthorizeHelper::class)->authorizeAny($actions);
    }
}

if (!function_exists('authorize_multiple')) {

    /**
     * @param array $actions
     * @return boolean
     */
    function authorize_multiple(array $actions)
    {
        return resolve(AuthorizeHelper::class)->authorizeMultiple($actions);
    }
}
