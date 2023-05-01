<?php

use App\Helpers\Core\General\ActivityHelper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

if (!function_exists('log_to_database')){

    /**
     * @param $description /Log title: example: A new role added
     * @param array $data Must contain an old value and new value should be in attributes
     * @param string $logName
     * @param null $auth Caused by. The user who is responsible for that
     * @param null $model
     * if you want translation you have to pass translate message as parameter
     * there is no default support for that
     * @return Builder|Model|object
     */
    function log_to_database($description, $data = [], $logName = 'default', $auth = null, $model = null){
        return resolve(ActivityHelper::class)
            ->logActivity($description, $data, $auth, $logName, $model);
    }
}


if (!function_exists('attach_log_to_database')) {
    function attach_log_to_database($pivot, $model, $old = [], $attribute = []) {
        log_to_database(trans('default.attach_log', [
            'pivot' => trans('default.'.$pivot),
            'model' => trans('default.'.$model),
        ]), [
            'old' => $old,
            'attributes' => $attribute
        ]);
    }
}


if (!function_exists('detach_log_to_database')) {
    function detach_log_to_database($pivot, $model, $old = [], $attribute = []) {
        log_to_database(trans('default.detach_log', [
            'pivot' => trans('default.'.$pivot),
            'model' => trans('default.'.$model),
        ]), [
            'old' => $old,
            'attributes' => $attribute
        ]);
    }
}

if (!function_exists('status_log_database')) {
    function status_log_database($model, $status, $old = [], $attribute = []) {
        log_to_database(trans('default.status_log', [
            'status' => trans('default.'.$status),
            'model' => trans('default.'.$model),
        ]), [
            'old' => $old,
            'attributes' => $attribute
        ]);
    }
}
