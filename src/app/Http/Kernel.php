<?php

namespace App\Http;

use App\Http\Middleware\AddAttendanceMiddleware;
use Illuminate\Foundation\Http\Kernel as HttpKernel;

/**
 * Class Kernel.
 */
class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \App\Http\Middleware\TrustProxies::class,
        \App\Http\Middleware\CheckForMaintenanceMode::class,
        \App\Http\Middleware\CheckForReadOnlyMode::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        // \App\Http\Middleware\SecureHeaders::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\Session\Middleware\AuthenticateSession::class, // Must be enabled for 'single login' to work
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \App\Http\Middleware\LocaleMiddleware::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\ToBeLoggedOut::class,
        ],

        'api' => [
            'throttle:60,1',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'admin' => [
            'auth',
            'authorize',
            'permission'
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'authorize' => \App\Http\Middleware\AuthorizeMiddleware::class,
        'permission' => \App\Http\Middleware\PermissionMiddleware::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password_expires' => \App\Http\Middleware\PasswordExpires::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'add.tenant' => \App\Http\Middleware\AddTenantMiddleware::class,
        'can_access' => \App\Http\Middleware\CheckAllAccessMiddleware::class,
        'employee_access' => \App\Http\Middleware\EmployeeProfileAuthorization::class,
        'add_attendance_middleware' => AddAttendanceMiddleware::class,
        'check_behavior' => \App\Http\Middleware\CheckAccessBehavior::class,
        'additional_behavior' => \App\Http\Middleware\AdditionalAccessBehavior::class,
        'request_show_all' => \App\Http\Middleware\SetShowAllRequest::class,
        'app.installed' => \App\Http\Middleware\CheckIfInstalledMiddleware::class,
        'app.not_install' => \App\Http\Middleware\CheckIfNotInstallMiddleware::class,
        'ifNotInstalled' => \App\Http\Middleware\CheckIfNotInstalledMiddleware::class,
        'valid_purchase_code' => \Gainhq\Installer\App\Middleware\ValidPurchaseCodeMiddleware::class,
    ];

    /**
     * The priority-sorted list of middleware.
     *
     * This forces non-global middleware to always be in the given order.
     *
     * @var array
     */
    protected $middlewarePriority = [
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \App\Http\Middleware\Authenticate::class,
        \Illuminate\Session\Middleware\AuthenticateSession::class,
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
        \Illuminate\Auth\Middleware\Authorize::class,
    ];
}
