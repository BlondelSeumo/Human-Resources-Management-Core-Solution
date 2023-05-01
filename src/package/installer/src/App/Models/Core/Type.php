<?php

namespace Gainhq\Installer\App\Models\Core;

use Spatie\Activitylog\Traits\LogsActivity;

class Type extends BaseModel
{
    use LogsActivity;

    protected $fillable = [
        'name', 'alias'
    ];
    protected static $logAttributes = [
        'name', 'alias'
    ];

    public static function findByAlias(string $alias)
    {
        return self::query()->whereAlias($alias)->first();
    }
}
