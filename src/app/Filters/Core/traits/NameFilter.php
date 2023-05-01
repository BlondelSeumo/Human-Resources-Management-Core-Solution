<?php


namespace App\Filters\Core\traits;


trait NameFilter
{
    public function name($name = null)
    {
        $this->whereClause('name', "%{$name}%", "LIKE");
    }
}
