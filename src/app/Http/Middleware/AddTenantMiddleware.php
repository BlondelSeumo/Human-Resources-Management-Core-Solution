<?php

namespace App\Http\Middleware;

use Closure;

class AddTenantMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (optional(tenant())->is_single) {
            $request->merge([
                'tenant_id' => tenant()->id,
                'tenant_short_name' => tenant()->short_name
            ]);
        }
        return $next($request);
    }
}
