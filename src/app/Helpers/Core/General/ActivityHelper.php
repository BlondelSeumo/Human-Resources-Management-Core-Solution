<?php


namespace App\Helpers\Core\General;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;

class ActivityHelper
{
    /**
     * @param $description /Log title: example: A new role added
     * @param array $data Must contain an old value and new value should be in attributes
     * @param null $auth Caused by. The user who is responsible for that
     * @param string $logName
     * @param null $model
     * if you want translation you have to pass translate message as parameter
     * there is no default support for that
     * @return Builder|Model|object|null
     */
    public function logActivity($description, $data = [], $auth = null, $logName = 'default', $model = null)
    {
        $auth = $auth ?? auth()->user();

        $activity = activity($logName)
            ->causedBy($auth);

        if ($model) {
            $activity->performedOn($model);
        }

        $activity->withProperties($data)
            ->log($description);

        return Activity::query()->latest()->first();
    }
}
