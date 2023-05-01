<?php


use App\Http\Controllers\Core\Auth\Role\PermissionController;
use App\Http\Controllers\Tenant\NavigationController;
use Illuminate\Routing\Router;

Route::group(['middleware' => ['auth', 'authorize']], function (Router $router) {
    $router->get('dashboard', [NavigationController::class, 'dashboard'])
        ->name('dashboard');

    $router->get('permissions', [PermissionController::class, 'index'])
        ->name('permissions')
        ->middleware('can:create_roles');

    include __DIR__.'/appearance.php';
    include __DIR__.'/dashboard.php';
});

Route::group(['middleware' => 'admin'], function () {

    include_route_files(__DIR__.'/feature/');

});
