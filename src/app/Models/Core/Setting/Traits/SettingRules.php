<?php


namespace App\Models\Core\Setting\Traits;


trait SettingRules
{
    public function createdRules()
    {
        return [
            'company_name' => 'required',
            'company_logo' => 'nullable|image',
            'date_format' => 'required|in:'.implode(',', config('settings.date_format')),
            'time_format' => 'required|in:'.implode(',', config('settings.time_format')),
            'time_zone' => 'required|in:'.implode(',', timezone_identifiers_list()),
            'number_of_decimal' => 'required|in:'.implode(',', config('settings.number_of_decimal')),
            'currency_position' => 'required|in:'.implode(',', config('settings.currency_position')),
        ];
    }

    public function updatedRules()
    {
        return [
            'settings' => 'required|array'
        ];
    }


    public function cornJobRules()
    {
        return [

        ];
    }

    public function settingMessages()
    {
        return [
            'date_format.in' => trans('validation.must_be',
                [
                    'name' => trans('default.date_format'),
                    'other' => implode(', ', config('settings.date_format'))
                ]),
            'time_format.in' => trans('validation.must_be',
                [
                    'name' => trans('default.time_format'),
                    'other' => implode(', ', config('settings.time_format'))
                ]),
            'number_of_decimal.in' => trans('validation.must_be',
                [
                    'name' => trans('default.number_of_decimal'),
                    'other' => implode(', ', config('settings.number_of_decimal'))
                ]),
            'currency_position.in' => trans('validation.must_be',
                [
                    'name' => trans('default.currency_position'),
                    'other' => implode(', ', config('settings.currency_position'))
                ]),
        ];
    }

}
