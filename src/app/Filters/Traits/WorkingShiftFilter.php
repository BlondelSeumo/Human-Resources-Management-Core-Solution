<?php


namespace App\Filters\Traits;


use App\Helpers\Traits\MakeArrayFromString;
use Illuminate\Database\Eloquent\Builder;

trait WorkingShiftFilter
{
    use MakeArrayFromString;

    public function workingShifts($working_shifts = null)
    {
        $working_shifts = $this->makeArray($working_shifts);

        $this->builder->when(
            count($working_shifts),
            fn (Builder $builder) => $builder->whereIn('working_shift_id', $working_shifts)
        );
    }

}