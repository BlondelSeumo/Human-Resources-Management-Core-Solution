<?php

namespace App\Http\Middleware;

use Closure;

/**
 * Class RedirectIfAuthenticated.
 */
class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (auth()->guard($guard)->check()) {
            $route = home_route();
            return redirect()->route(
                $route['route_name'],
                ['params' => $route['route_params']]
            );
        }

        return $next($request);
    }
}
