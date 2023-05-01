<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetShowAllRequest
{
    public function handle(Request $request, Closure $next)
    {
        $request->merge([
            'show_all' => 'yes'
        ]);

        return $next($request);
    }
}
