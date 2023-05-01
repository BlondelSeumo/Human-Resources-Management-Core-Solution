<?php

namespace Gainhq\Installer\App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    protected static $logOnlyDirty = true;

    public function createdRules()
    {
        return [
            //
        ];
    }

    public function updatedRules()
    {
        return $this->createdRules();
    }
}
