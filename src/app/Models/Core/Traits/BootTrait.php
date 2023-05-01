<?php

namespace  App\Models\Core\Traits;

trait BootTrait {
    public static function boot()
    {
        parent::boot();
        if (!app()->runningInConsole()){
            static::creating(function($model){
                return $model->fill([
                    'created_by' => $model->created_by ?: auth()->id()
                ]);
            });
        }
    }
}
