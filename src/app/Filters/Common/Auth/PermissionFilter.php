<?php


namespace App\Filters\Common\Auth;


use App\Filters\Common\FilterContact;
use App\Helpers\Core\Traits\InstanceCreator;
use Illuminate\Database\Eloquent\Builder;

class PermissionFilter extends FilterContact
{
    use InstanceCreator;

    function filter(): Builder
    {
        return $this->query;
    }
}