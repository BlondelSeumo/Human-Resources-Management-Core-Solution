<?php

namespace Gainhq\Installer\App\Middleware;

use Closure;
use Gainhq\Installer\App\Exceptions\GeneralException;
use Illuminate\Http\Request;

class CheckIfNotInstalledMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        throw_if(
            app()->environment('production') && env('APP_INSTALLED', false),
            new GeneralException(trans('default.action_not_allowed'))
        );

        return $next($request);
    }
}
