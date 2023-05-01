<?php


namespace App\Http\Controllers\Core\Builder\Form;

use App\Filters\Core\BaseFilter;
use App\Http\Controllers\Controller;
use App\Models\Core\Builder\Form\CustomFieldType;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class CustomFieldTypeController extends Controller
{

    public function __construct(BaseFilter $filter)
    {
        $this->filter = $filter;
    }

    /**
     * @return CustomFieldType[]|Collection
     */
    public function index()
    {
        return CustomFieldType::query()
            ->filters($this->filter)
            ->get();
    }

}
