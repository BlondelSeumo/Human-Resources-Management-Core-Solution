<?php

namespace App\Models\Core\Auth;

use Illuminate\Database\Eloquent\Relations\Pivot;


class RolePermissionPivot extends Pivot
{
    protected $table = 'role_permission';

    protected $casts = [
        'meta' => 'array'
    ];
}
