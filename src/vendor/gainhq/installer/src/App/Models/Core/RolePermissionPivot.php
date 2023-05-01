<?php

namespace Gainhq\Installer\App\Models\Core;

use Illuminate\Database\Eloquent\Relations\Pivot;


class RolePermissionPivot extends Pivot
{
    protected $table = 'role_permission';

    protected $casts = [
        'meta' => 'array'
    ];
}
