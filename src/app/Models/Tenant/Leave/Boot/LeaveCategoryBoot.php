<?php


namespace App\Models\Tenant\Leave\Boot;


use Illuminate\Support\Str;

trait LeaveCategoryBoot
{
    public static function boot()
    {
        parent::boot();
        static::creating(function($model){
            if (!$model->alias) {
                return $model->fill([
                    'alias' => Str::slug($model->name)
                ]);
            }
        });
    }
}
