<?php

namespace Gainhq\Installer\App\Middleware;

use Closure;
use Gainhq\Installer\App\Exceptions\GeneralException;
use Illuminate\Http\Request;

class ValidPurchaseCodeMiddleware
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
        return $next($request);
    }

}