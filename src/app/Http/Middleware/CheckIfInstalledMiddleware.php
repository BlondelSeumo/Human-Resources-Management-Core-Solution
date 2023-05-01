<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckIfInstalledMiddleware
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
        if (app()->environment('production')){
            if (!config('gain.installed') && !$request->expectsJson())
                return redirect(request()->root().'/install');
        }
        return $next($request);
    }
}
