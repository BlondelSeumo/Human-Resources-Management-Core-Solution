<?php


namespace App\Models\Tenant\WorkingShift\Relationship;


use App\Models\Tenant\WorkingShift\WorkingShift;

trait WorkingShiftDetailsRelationship
{
    public function workingShift()
    {
        return $this->belongsTo(WorkingShift::class);
    }

}
