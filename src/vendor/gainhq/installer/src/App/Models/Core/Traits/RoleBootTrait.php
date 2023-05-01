<?php


namespace Gainhq\Installer\App\Models\Core\Traits;

use Gainhq\Installer\App\Hooks\AfterRolePivotAction;
use Gainhq\Installer\App\Hooks\AfterRoleSaved;

trait RoleBootTrait
{
    public static function boot()
    {
        parent::boot();

        if (!app()->runningInConsole()) {
            static::creating(function($model){
                if (!$model->created_by) {
                    return $model->fill([
                        'created_by' => auth()->id()
                    ]);
                }
            });
        }

        static::updated(function (Role $role) {
            AfterRoleSaved::new(true)
                ->setModel($role)
                ->handle();
        });

        static::synced(function ($role, $relation, $properties) {
            AfterRolePivotAction::new()
                ->setModel($role)
                ->setRelation($relation)
                ->setProperties($properties)
                ->setType('synced')
                ->handle();
        });

        static::attached(function ($role, $relation, $properties) {
            AfterRolePivotAction::new()
                ->setModel($role)
                ->setRelation($relation)
                ->setProperties($properties)
                ->setType('attached')
                ->handle();
        });

        static::updatingExistingPivot(function ($role, $relation, $properties) {
            AfterRolePivotAction::new()
                ->setModel($role)
                ->setRelation($relation)
                ->setProperties($properties)
                ->setType('updatingExistingPivot')
                ->handle();
        });
    }
}
