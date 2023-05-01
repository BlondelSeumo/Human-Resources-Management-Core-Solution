<?php

namespace Gainhq\Installer\App\Models\Core\Traits;

use Gainhq\Installer\App\Hooks\AfterUserPivotAction;
use Gainhq\Installer\App\Hooks\AfterUserSaved;
use Gainhq\Installer\App\Hooks\WhileUserDeleting;

trait UserBootTrait
{
    public static function boot() : void
    {
        parent::boot();
        if (!app()->runningInConsole()){
            static::creating(function($model){
                return $model->fill([
                    'created_by' => auth()->id()
                ]);
            });
        }

        static::saved(function ($user) {
            AfterUserSaved::new(true)
                ->setModel($user)
                ->handle();
        });

        static::synced(function ($user, $relation, $properties) {
            AfterUserPivotAction::new(true)
                ->setModel($user)
                ->setProperties($properties)
                ->setRelation($relation)
                ->setType('synced')
                ->handle();
        });

        static::attached(function ($user, $relation, $properties) {
            AfterUserPivotAction::new(true)
                ->setModel($user)
                ->setProperties($properties)
                ->setRelation($relation)
                ->setType('attached')
                ->handle();
        });

        static::detached(function ($user, $relation, $properties) {
            AfterUserPivotAction::new(true)
                ->setModel($user)
                ->setProperties($properties)
                ->setRelation($relation)
                ->setType('detached')
                ->handle();
        });

        static::updatingExistingPivot(function ($user, $relation, $properties) {
            AfterUserPivotAction::new(true)
                ->setModel($user)
                ->setProperties($properties)
                ->setRelation($relation)
                ->setType('updatingExistingPivot')
                ->handle();
        });

        static::deleting(function (User $user) {
            WhileUserDeleting::new(true)
                ->setModel($user)
                ->handle();
        });

    }
}
