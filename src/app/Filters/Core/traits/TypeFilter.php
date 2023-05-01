<?php


namespace App\Filters\Core\traits;


use Illuminate\Database\Eloquent\Builder;

trait TypeFilter
{
    public function typeId($id = null)
    {
        $this->whereClause('type_id', $id, $this->typeIdOperator);
    }

    public function type($type = null)
    {
        $this->builder->whereHas('type', function (Builder $query) use ($type) {
            $query->where('alias', $type);
        })->orWhereNull('type_id');
    }
}
