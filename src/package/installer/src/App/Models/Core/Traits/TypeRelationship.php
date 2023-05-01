<?php

namespace Gainhq\Installer\App\Models\Core\Traits;

use Gainhq\Installer\App\Models\Core\Type;

trait TypeRelationship
{
    public function type()
    {
        return $this->belongsTo(Type::class);
    }
}
