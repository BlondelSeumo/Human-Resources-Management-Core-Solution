<?php

namespace App\Http\Controllers\Core\Setting;

use App\Filters\FilterBuilder;
use App\Filters\Core\BaseFilter;
use App\Http\Controllers\Controller;
use App\Models\Core\Auth\Type;

class TypeController extends Controller
{
    public function __construct(BaseFilter $filter)
    {
        $this->filter = $filter;
    }


    /**
     * @return mixed
     */
    public function index()
    {
        return Type::query()
            ->filters($this->filter)
            ->get();
    }
}
