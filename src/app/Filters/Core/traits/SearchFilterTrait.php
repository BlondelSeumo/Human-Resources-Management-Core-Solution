<?php


namespace App\Filters\Core\traits;


trait SearchFilterTrait
{
    public function search($search = null)
    {
        $this->name($search);
    }
}
