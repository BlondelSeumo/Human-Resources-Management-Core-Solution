<?php

use App\Http\Controllers\Tenant\WorkingShift\WorkingShiftController;
use App\Http\Controllers\Tenant\WorkingShift\WorkingShiftUserController;
use Illuminate\Routing\Router;

Route::group(['prefix' => 'app', 'middleware' => 'check_behavior'], function (Router $router) {
    $router->post('working-shifts/{working_shift}/add-employees', [WorkingShiftUserController::class, 'store'])
        ->name('working-shift.add-employees-to');

    $router->get('working-shifts/{working_shift}/users', [WorkingShiftUserController::class, 'index'])
        ->name('working-shift.add-employees-to');

    $router->apiResource('working-shifts', WorkingShiftController::class)->except('show');
});
