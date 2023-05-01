<?php

use Gainhq\Installer\App\Controllers\InstallerController;
use Gainhq\Installer\App\Controllers\Setup\AppUpdateController;
use Gainhq\Installer\App\Controllers\Setup\BroadcastSettingController;
use Gainhq\Installer\App\Controllers\Setup\DeliverySettingController;
use Gainhq\Installer\App\Controllers\Setup\EnvironmentController;
use Gainhq\Installer\App\Controllers\Setup\PurchaseCodeController;
use Illuminate\Support\Facades\Route;

//Before purchase code validation
Route::group(['prefix' => 'setup', 'middleware' => ['guest', 'ifNotInstalled']], function () {

    Route::get('additional-requirement', [EnvironmentController::class, 'additionalIndex'])
        ->name('additional_requirements.additional-requirement');

    Route::get('additional-requirements', [EnvironmentController::class, 'additionalRequirements'])
        ->name('additional_requirements.additional-requirements');

    Route::post('generate-url', [EnvironmentController::class, 'generateUrl']);

    Route::get('purchase-code', [PurchaseCodeController::class, 'index']);

    Route::post('purchase-code-store', [PurchaseCodeController::class, 'purchaseCodeStore'])
        ->name('purchase-code.store');
});


//After purchase code validation
Route::group(['prefix' => 'setup', 'middleware' => ['guest', 'ifNotInstalled', 'valid_purchase_code']], function () {

    //Database
    Route::get('get-database-hostname', [EnvironmentController::class, 'getHostName']);

    Route::get('database', [EnvironmentController::class, 'index'])
        ->name('database_configuration.index');

    Route::post('store-database-config', [EnvironmentController::class, 'store']);

    //Admin Info
    Route::get('admin-info', [EnvironmentController::class, 'show'])
        ->name('admin_info.admin-info');

    Route::post('store-admin-info', [EnvironmentController::class, 'update']);

    //Email set up
    Route::get('email-setup', [EnvironmentController::class, 'emailSetup'])
        ->name('email_setup.email-setup');

    Route::post('email-setting-update-delivery', [DeliverySettingController::class, 'update'])
        ->name('email_setup.email-setting-update-delivery');

    Route::post('email-setup-skip', [EnvironmentController::class, 'skipEmailSetup'])
        ->name('email_setup.email-setup-skip');

    //Broadcast set up
    Route::get('broadcast-setup', [EnvironmentController::class, 'broadcastSetup'])
        ->name('broadcast_setup.broadcast-setup');

    Route::post('broadcast-setting-update', [BroadcastSettingController::class, 'update'])
        ->name('broadcast_setup.broadcast-setting-update');

    Route::post('broadcast-skip', [EnvironmentController::class, 'skipBroadCast'])
        ->name('broadcast_setup.broadcast-skip');
});

/*
 App update api
 * */
Route::get('app/updates', [AppUpdateController::class, 'index']);
Route::get('app/generated-update-url-purchase-code', [AppUpdateController::class, 'getUpdateUrl']);
Route::post('app/updates/install/{version}', [AppUpdateController::class, 'update']);
Route::get('/storage-link',[InstallerController::class, 'testStorageLink']);
Route::get('/php-info-test',[InstallerController::class, 'phpInfo']);
Route::post('app/v2/manual-update', [AppUpdateController::class, 'manualUpdate']);
Route::get('app/v2/manual-update/urlInfo', [AppUpdateController::class, 'urlInfo']);
Route::get('app/v2/manual-update/generate-download-file-url', [AppUpdateController::class, 'generateDownloadFileUrl']);
Route::get('/app/clear-cache',[EnvironmentController::class, 'clearCache']);