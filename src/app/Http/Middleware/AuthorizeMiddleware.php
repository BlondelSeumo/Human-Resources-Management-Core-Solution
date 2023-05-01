<?php

namespace App\Http\Middleware;

use App\Models\Core\Auth\Role;
use App\Models\Core\Auth\User;
use App\Repositories\Core\Auth\UserRepository;
use Closure;
use Illuminate\Support\Facades\Gate;

class AuthorizeMiddleware
{

    protected $user;

    public function __construct(UserRepository $user)
    {
        $this->user = $user;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     * @throws \Exception
     */
    public function handle($request, Closure $next)
    {
        if (auth()->user()->isAppAdmin())
            return $next($request);

        if (auth()->user()->isInactive() || auth()->user()->isTerminated()) {
            auth()->logout();
            return redirect()->route('users.login.index');
        }

        $user = $this->user->getCachedAuthUserWithRoleAndPermissions();

        optional($user->roles)->map(function (Role $role) {
            optional($role->permissions)->map(function ($permission) {
                Gate::define($permission->name, function (User $user) {
                    return true;
                });
            });
        });

        Gate::define('manage_brand_dashboard', function (User $user) {
            return true;
        });

        Gate::define('manage_dashboard', function (User $user) {
            return true;
        });

        return $next($request);
    }
}
