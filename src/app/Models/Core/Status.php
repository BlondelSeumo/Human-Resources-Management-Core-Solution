<?php

namespace App\Models\Core;

use App\Models\Core\Traits\Translate\TranslatedNameTrait;

class Status extends BaseModel
{
    protected $appends = ['translated_name'];
    use TranslatedNameTrait;
    protected $fillable = ['name', 'type', 'class'];

    public static function findByNameAndType($name, $type = 'user')
    {
        return self::query()
            ->where('name', $name)
            ->where('type', $type)
            ->first();
    }

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($status) {
            cache()->forget('statuses');
            cache()->forget('statuses-'.optional($status)->type);
        });

        static::deleting(function ($status) {
            cache()->forget('statuses');
            cache()->forget('statuses-'.optional($status)->type);
        });
    }
}
