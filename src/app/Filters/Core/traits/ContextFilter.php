<?php


namespace App\Filters\Core\traits;


trait ContextFilter
{
    public function context($context = null)
    {
        $this->whereClause('context', $context, $this->contextOperator);
    }
}
