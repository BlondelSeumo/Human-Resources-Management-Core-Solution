<?php

namespace App\Http\Middleware;

use App\Helpers\Traits\AutoApprovalTrait;
use App\Helpers\Traits\SettingKeyHelper;
use Closure;
use Illuminate\Http\Request;

class CheckAllAccessMiddleware
{
    use SettingKeyHelper, AutoApprovalTrait;

    public function handle(Request $request, Closure $next, $permissions)
    {
        $access = 'no';

        if (auth()->user()->can($permissions)) {
            $access = 'yes';
        }

        $request->merge([
            'show_all' => $access
        ]);

        return $next($request);
    }
}
