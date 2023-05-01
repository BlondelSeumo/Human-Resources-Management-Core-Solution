<?php


namespace App\Models\Core\Log;


use App\Filters\FilterBuilder;
use Spatie\Activitylog\Models\Activity;


class ActivityLog extends Activity
{
    public function scopeFilters($query, FilterBuilder $filter)
    {
        return $filter->apply($query);
    }
}
