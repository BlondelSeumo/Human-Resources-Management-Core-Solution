<?php

use App\Http\Controllers\Common\Setting\SettingsFormatController;

Route::get('app/settings-format', [SettingsFormatController::class, 'configs'])
    ->name('settings.config');