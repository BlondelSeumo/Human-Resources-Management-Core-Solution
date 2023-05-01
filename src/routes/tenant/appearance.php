<?php

use App\Http\Controllers\Tenant\NavigationController;

Route::get('user/my-profile', [NavigationController::class, 'profile'])
    ->name('user.profile');
