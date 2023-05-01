<?php

use App\Http\Controllers\Core\Auth\User\PasswordResetController;
use App\Http\Controllers\Core\Auth\UserConfirmController;

Route::get('confirm', [ UserConfirmController::class, 'index' ])
    ->name('user-invite.index')
    ->middleware('signed');

Route::post('confirm', [ UserConfirmController::class, 'confirm' ])
    ->name('user-invite.confirm');

Route::get('password-reset', [ PasswordResetController::class, 'index' ])
    ->name('password-reset.index');
Route::post('password-reset', [ PasswordResetController::class, 'store' ])
    ->name('password-reset.store');

Route::get('reset-password', [ PasswordResetController::class, 'show' ])
    ->name('reset-password.index')
    ->middleware('signed');

Route::post('reset-password', [PasswordResetController::class, 'update'])
    ->name('reset-password.store');
