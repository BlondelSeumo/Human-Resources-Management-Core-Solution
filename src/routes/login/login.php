<?php

use App\Http\Controllers\Core\Auth\User\LoginController;

Route::get('login', [LoginController::class, 'show'])
    ->name('users.login.index');

Route::post('login', [LoginController::class, 'login'])
    ->name('users.login');
