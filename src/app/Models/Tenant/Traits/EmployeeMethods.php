<?php


namespace App\Models\Tenant\Traits;


use App\Models\Tenant\Employee\EmploymentStatus;
use Illuminate\Database\Eloquent\Collection;

trait EmployeeMethods
{
    public function isTerminated()
    {
        if ($this->employmentStatus instanceof EmploymentStatus) {
            return $this->employmentStatus->alias === 'terminated';
        }

        if ($this->employmentStatus instanceof Collection && $this->employmentStatus->count()) {
            return $this->employmentStatus->first()->alias === 'terminated';
        }

        return false;

    }

    public function isInProbation()
    {
        if ($this->employmentStatus instanceof EmploymentStatus) {
            return $this->employmentStatus->alias === 'probation';
        }

        if ($this->employmentStatus instanceof Collection && $this->employmentStatus->count()) {
            return $this->employmentStatus->first()->alias === 'probation';
        }

        return false;

    }

    public function isPermanent()
    {
        if ($this->employmentStatus instanceof EmploymentStatus) {
            return $this->employmentStatus->alias === 'permanent';
        }

        if ($this->employmentStatus instanceof Collection && $this->employmentStatus->count()) {
            return $this->employmentStatus->first()->alias === 'permanent';
        }

        return false;

    }
}