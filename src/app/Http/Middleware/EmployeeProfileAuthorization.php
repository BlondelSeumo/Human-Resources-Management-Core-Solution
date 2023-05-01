<?php

namespace App\Http\Middleware;

use App\Exceptions\GeneralException;
use App\Helpers\Route\RouteToPermissionString;
use App\Models\Core\Auth\User;
use Closure;
use Illuminate\Http\Request;

class EmployeeProfileAuthorization
{
    use RouteToPermissionString;

    public function handle(Request $request, Closure $next)
    {
        /** @var User $employee */
        $employee = optional($request->route())->parameter('employee');

        $all = 'no';

        $route_name = preg_replace('/core.|app.|tenant.|support./', '', $request->route()->getName(), 1);

        $permission_string = $this->setRouteName($route_name)
            ->validateRouteName()
            ->getPermissionString();

        if ($employee && !(authorize_any([ $permission_string ]) || $employee->id == auth()->id())) {
            throw new GeneralException(trans('default.action_not_allowed'));
        }

        if (authorize_any([ $permission_string ])) {
            $all = 'yes';
        }

        $request->merge(['all' => $all]);

        return $next($request);
    }
}
