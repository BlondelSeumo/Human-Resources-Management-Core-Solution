<?php

use App\Http\Controllers\Core\Builder\Form\CustomFieldController;
use App\Http\Controllers\Core\Setting\NotificationSettingController;
use App\Http\Controllers\Tenant\Settings\GeneralSettingController;
use Illuminate\Routing\Router;

Route::patch('notification/settings/{notification_setting}', [NotificationSettingController::class, 'update'])
    ->name('notification-settings.update');

Route::apiResource('custom-fields', CustomFieldController::class);

Route::group(['prefix' => 'general'], function (Router $router) {
    $router->get('settings', [GeneralSettingController::class, 'index'])
        ->name('settings.index');

    $router->post('settings', [GeneralSettingController::class, 'update'])
        ->name('settings.update');
});
