<?php


namespace App\Filters\Core\traits;


trait CreatedByFilter
{
    public function createdBy($id = null)
    {
        $this->whereClause('created_by', $id, $this->createdByOperator);
    }
}
