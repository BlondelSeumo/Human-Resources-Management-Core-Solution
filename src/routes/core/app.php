<?php


use App\Http\Controllers\Core\{
    Log\ActivityLogController,
    Notification\NotificationEventTemplateController,
    Notification\NotificationTemplateController,
    Builder\Form\CustomFieldController,
    Setting\CornJobSettingController,
    Setting\DeliverySettingController,
    Setting\NotificationSettingController,
    Setting\SettingController};

Route::group(['prefix' => 'app'], function () {

    Route::get('settings', [SettingController::class, 'index'])
        ->name('settings.index');

    Route::post('settings', [SettingController::class, 'update'])
        ->name('settings.update');

    Route::get('settings/delivery-settings', [DeliverySettingController::class, 'index'])
        ->name('settings.view-delivery');

    Route::post('settings/delivery-settings', [DeliverySettingController::class, 'update'])
        ->name('settings.update-delivery');

    Route::get('settings/delivery-settings/{provider}', [DeliverySettingController::class, 'show'])
        ->name('settings.view_delivery');


    Route::get('settings/corn-job-settings', [CornJobSettingController::class, 'index'])
        ->name('settings.view-corn-job');

    Route::post('settings/corn-job-settings', [CornJobSettingController::class, 'update'])
        ->name('settings.update-corn-job');

    Route::resource('custom-fields', CustomFieldController::class);
    Route::resource('notification-settings', NotificationSettingController::class)
        ->only('show', 'update');


    Route::resource('notification-templates', NotificationTemplateController::class)
        ->except('create', 'edit');

    Route::post('notification-events/{event}/attach-templates', [NotificationEventTemplateController::class, 'store'])
        ->name('notification-events.attach-templates');

    Route::post('notification-events/{event}/detach-templates', [NotificationEventTemplateController::class, 'update'])
    ->name('notification-events.detach-templates');

    Route::get('activity-logs', [ActivityLogController::class, 'index'])
        ->name('activity-logs.index');


});
