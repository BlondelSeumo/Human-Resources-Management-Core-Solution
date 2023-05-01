<?php

namespace App\Http\Controllers\Core\Auth\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Core\Auth\User\LoginRequest as Request;
use App\Services\Core\Auth\UserService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use App\Hooks\User\CustomRoute;
class LoginController extends Controller
{
    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    public function show()
    {
        $demoCredentials = env('IS_DEMO', false) ? config('demo.loginCredentials') : [];
        return env('APP_INSTALLED') ? view('auth.login')->with('demo', $demoCredentials) : redirect('install');
    }

    /**
     * @param Request $request
     * @return string
     */
    public function login(Request $request)
    {
        try {
            $this->service->login();
            // custom hook
            $route = CustomRoute::new(true)->handle();
            $route = count($route) ? $route : home_route();
            return route(
                $route['route_name'],
                $route['route_params']
            );
        } catch (\Exception $exception) {
            return response()->json([
                'message' => $exception instanceof ModelNotFoundException ? trans('default.resource_not_found', ['resource' => trans('default.user')]) : $exception->getMessage()
            ], 400);
        }
    }

    public function logOut(): RedirectResponse
    {
        session()->flush();
        auth()->logout();
        session()->flush();

        return redirect()->route('users.login.index');
    }
}
