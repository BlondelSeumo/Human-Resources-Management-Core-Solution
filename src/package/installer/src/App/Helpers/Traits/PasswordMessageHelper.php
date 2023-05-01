<?php

namespace Gainhq\Installer\App\Helpers\Traits;

trait PasswordMessageHelper
{
    public function messages()
    {
        return array_merge(parent::messages(), [
            'password.regex' => trans('default.password_must_contains_things')
        ]);
    }
}
