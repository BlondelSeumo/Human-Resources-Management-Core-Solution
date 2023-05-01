<?php


namespace App\Filters\Core\traits;


trait StatusIdFilter
{
    public function statusId($id = null)
    {
        $this->whereClause('status_id', $id, $this->statusIdOperator);
    }
}
