<?php


namespace App\Filters\Traits;


trait DepartmentFilterTrait
{
    public function departments($value = null)
    {
        $this->whereClause('department_id', $value);
    }
}
