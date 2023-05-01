<?php

namespace App\Services\Tenant\Holiday;

use App\Exceptions\GeneralException;
use App\Helpers\Core\Traits\HasWhen;
use App\Helpers\Traits\DateTimeHelper;
use App\Models\Tenant\Holiday\Holiday;
use App\Services\Tenant\TenantService;
use Illuminate\Database\Eloquent\Builder;

class HolidayService extends TenantService
{
    use DateTimeHelper, HasWhen;

    protected $holidayDepartments = [];

    public function __construct(Holiday $holiday )
    {
        $this->model = $holiday;
    }

    public function validateForDelete($holiday): self
    {
        throw_if(
            $this->carbon($holiday->start_date)->parse()->isPast(),
            new GeneralException(__t('cant_update_holidays'))
        );

        return $this;
    }

    public function validateForUpdate($holiday): self
    {
        $startDate = $this->carbon($holiday->start_date)->parse();

        throw_if(
            $startDate->isPast() && !$startDate->isCurrentYear(),
            new GeneralException(__t('cant_update_holidays'))
        );

        return $this;
    }

    public function validateUpdateRequest($holiday): self
    {
        $startDate = $this->carbon($holiday->start_date)->parse();

        $this->when(!$startDate->isPast(),
            function (HolidayService $service) use($holiday) {
                validator($service->getAttributes(), $holiday->createdRules())->validate();
            },
        );


        return $this;
    }

    public function timePeriod($builder, $period = null)
    {
        $period = json_decode(htmlspecialchars_decode($period), true);

        $builder->when($period, function (Builder $builder) use ($period) {
            $builder->where(function (Builder $builder) use ($period) {
                $builder->whereBetween('start_date', array_values($period))
                    ->orWhereBetween('end_date', array_values($period))
                    ->orWhere(function ($query) use ($period) {
                        $query->whereDate('start_date', '<=', $period['start'])
                            ->whereDate('end_date', '>=', $period['end']);
                    });
            });
        });
    }
}
