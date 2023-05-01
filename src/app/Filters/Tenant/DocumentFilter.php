<?php

namespace App\Filters\Tenant;

use App\Filters\Core\traits\NameFilter;
use App\Filters\Core\traits\SearchFilterTrait;
use App\Filters\FilterBuilder;

class DocumentFilter extends FilterBuilder
{
    use NameFilter, SearchFilterTrait;

    public function userId($id = null)
    {
        if($id) {
            $this->whereClause('user_id', $id, "=");
        }
    }
}