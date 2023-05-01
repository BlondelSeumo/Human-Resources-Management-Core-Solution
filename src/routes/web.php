<?php

use App\Http\Controllers\Core\LanguageController;
use App\Http\Controllers\DocumentationController;
use App\Http\Controllers\InstallDemoDataController;
use App\Http\Middleware\PermissionMiddleware;
use Illuminate\Support\Facades\Route;

Route::redirect('/', url('admin/users/login'));

Route::view('/welcome', 'welcome'); // temporary
Route::get("doc/core/components", [\App\Http\Controllers\DocumentationController::class,'index']);
Route::get("doc/core/components/{component_name}", [\App\Http\Controllers\DocumentationController::class,'show']);


// Switch between the included languages
Route::get('lang/{lang}', [LanguageController::class, 'swap'])->name('language.change');

/*
 * All login related route will be go there
 * Only guest user can access this route
 */

Route::group(['middleware' => ['app.installed', 'guest'], 'prefix' => 'users'], function () {
    include_route_files(__DIR__.'/user/');
});

Route::group(['middleware' => ['app.installed', 'guest'], 'prefix' => 'admin/users'], function () {
    include_route_files(__DIR__.'/login/');
});

/**
 * This route is only for brand redirection
 * And for some additional route
 */
Route::group(['prefix' => 'admin', 'middleware' => ['app.installed', 'auth', 'authorize']], function () {
    include  __DIR__.'/additional.php';
});

Route::group(['middleware' => ['auth', 'authorize'], 'as' => 'support.'], function () {
    include_route_files(__DIR__.'/support/');
});

Route::group(['as' => 'tenant.', 'middleware' => ['app.installed', 'add.tenant']], function () {
    include __DIR__.'/tenant/index.php';
});

/**
 * Backend Routes
 * Namespaces indicate folder structure
 * All your route in sub file must have a name with not more than 2 index
 * Example: brand.index or dashboard
 * See @var PermissionMiddleware for more information
 */
Route::group(['prefix' => 'admin', 'middleware' => 'admin', 'as' => 'core.'], function () {

    /*
     * (good if you want to allow more than one group in the core,
     * then limit the core features by different roles or permissions)
     *
     * Note: Administrator has all permissions so you do not have to specify the administrator role everywhere.
     * These routes can not be hit if the password is expired
     */
    include_route_files(__DIR__ . '/core/');

});



/*
 * This routes is for documentation purpose.
 */

Route::get("doc/core/components", [DocumentationController::class,'index']);
Route::get("doc/core/components/{component_name}", [DocumentationController::class,'show']);

Route::any('install-demo-data', [InstallDemoDataController::class, 'run'])
    ->name('install-demo-data');

Route::get('link', function(){
    $target = storage_path("app/public");
    $explode_base_path = explode(DIRECTORY_SEPARATOR, base_path());
    array_pop($explode_base_path);
    array_push($explode_base_path, 'storage');
    $path = implode(DIRECTORY_SEPARATOR, $explode_base_path);
    symlink($target, $path);
    return true;
});