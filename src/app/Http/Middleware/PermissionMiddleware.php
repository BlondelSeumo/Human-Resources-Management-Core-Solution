<?php

namespace App\Http\Middleware;

use App\Exceptions\GeneralException;
use App\Helpers\Route\RouteToPermissionString;
use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class PermissionMiddleware
{
    use RouteToPermissionString;

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param \Closure $next
     * @return mixed
     * @throws GeneralException
     * @throws AuthorizationException
     *
     */
    public function handle($request, Closure $next)
    {
        $route_name = preg_replace('/core.|app.|tenant./', '', $request->route()->getName(), 1);

        if (!$route_name) {
            throw new GeneralException('Route must have a name');
        }

        /**
         * Avoid if the user is appAdmin
         */
        if (auth()->user()->isAppAdmin()) {
            return $next($request);
        }

        $permission_string = $this->setRouteName($route_name)
            ->validateRouteName()
            ->getPermissionString();

        /*
         * Authorizing user with permission and merge allowed resource into allowed_resource index
         * if the allowed_resource is empty array then all resource is allowed
         * otherwise only take what in that array
         * allowed resource will contain ID's of model which is permitted for currently logged in user
         */
        $meta = get_authorized($permission_string);

        $request->merge([
            'allowed_resource' => $meta
        ]);

        return $next($request);
    }

}
