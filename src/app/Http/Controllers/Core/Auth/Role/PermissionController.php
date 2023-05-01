<?php

namespace App\Http\Controllers\Core\Auth\Role;

use App\Filters\Common\Auth\PermissionFilter;
use App\Filters\Core\BaseFilter;
use App\Http\Controllers\Controller;
use App\Models\Core\Auth\Permission;
use Illuminate\Database\Eloquent\Collection;

class PermissionController extends Controller
{

    public function __construct(BaseFilter $filter)
    {
        $this->filter = $filter;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Permission[]|Collection
     */
    public function index()
    {
        return PermissionFilter::new(true, Permission::query()
            ->filters($this->filter))
            ->filter()
            ->get()
            ->when(!request()->without_group, function (Collection $query) {
                return $query->groupBy(function ($permission) {
                    return $permission->group_name;
                });
            });
    }

}
