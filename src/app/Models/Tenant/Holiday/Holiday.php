<?php

namespace App\Models\Tenant\Holiday;

use App\Helpers\Traits\DateRangeHelper;
use App\Models\Tenant\Holiday\Relationship\HolidayRelationship;
use App\Models\Tenant\Holiday\role\HolidayRules;
use App\Models\Tenant\TenantModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class Holiday extends TenantModel
{
    use HolidayRules, HolidayRelationship;

    use DateRangeHelper;

    protected $fillable = ['name', 'start_date', 'end_date', 'description', 'repeats_annually', 'tenant_id'];

    public function scopeRanges(Builder $builder, $ranges)
    {
        if (count($ranges) == 1) {
            return $builder->whereBetween(DB::raw('DATE(start_date)'), [$ranges[0], $ranges[0]])
                ->whereBetween(DB::raw('DATE(end_date)'), [$ranges[0], $ranges[0]]);
        }

        return $builder->whereBetween(DB::raw('DATE(start_date)'), $ranges)
            ->whereBetween(DB::raw('DATE(end_date)'), $ranges);
    }

    public function scopeGeneral(Builder $builder): void
    {
        $builder->whereDoesntHave('departments');
    }

    public function scopeWhereDepartments(Builder $builder, array $departments): void
    {
        $builder->whereHas(
            'departments',
            fn(Builder $bl) => $bl->whereIn('id', $departments)
        );
    }

    public static function getDatesFromHolidays($holidays)
    {
        return $holidays->reduce(fn($holidays, Holiday $holiday) => array_merge(
            $holidays,
            (new static())->dateRange(Carbon::parse($holiday->start_date, 'UTC'), Carbon::parse($holiday->end_date, 'UTC'))
        ), []);
    }

    public static function generalHolidays($ranges): Collection
    {
        return self::ranges($ranges)->general()->get(['id', 'name', 'start_date', 'end_date']);
    }
}
