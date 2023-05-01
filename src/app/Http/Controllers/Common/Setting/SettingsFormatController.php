<?php

namespace App\Http\Controllers\Common\Setting;

use App\Http\Controllers\Controller;

class SettingsFormatController extends Controller
{
    public function configs()
    {
        return [
            'time_format' => config('settings.time_format'),
            'currency_position' => config('settings.currency_position'),
            'date_format' => config('settings.date_format'),
            'decimal_separator' => config('settings.decimal_separator'),
            'thousand_separator' => config('settings.thousand_separator'),
            'number_of_decimal' => config('settings.number_of_decimal'),
            'time_zones' => timezone_identifiers_list(),
            'mail_context' => array_keys(config('settings.supported_mail_services')),
            'use_for' => [
                'notification',
                'campaign',
                'test'
            ],
            'context' => config('settings.context')
        ];
    }
}
