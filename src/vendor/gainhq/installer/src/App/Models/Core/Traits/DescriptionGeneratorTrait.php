<?php

namespace Gainhq\Installer\App\Models\Core\Traits;

use Illuminate\Support\Str;

trait DescriptionGeneratorTrait
{
    public function getDescriptionForEvent(string $eventName): string
    {
        $class_alias_array = explode('\\', get_called_class());
        $class_name = Str::snake(end($class_alias_array));

        return trans('default.log_description_message', [
            'model' => trans('default.' . $class_name),
            'event' => trans('default.' . $eventName)
        ]);
    }
}
