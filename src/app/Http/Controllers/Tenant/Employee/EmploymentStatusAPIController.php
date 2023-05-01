<?php


namespace App\Http\Controllers\Tenant\Employee;


use App\Helpers\Traits\MakeArrayFromString;
use App\Http\Controllers\Controller;
use App\Models\Tenant\Employee\EmploymentStatus;
use Illuminate\Database\Eloquent\Builder;

class EmploymentStatusAPIController extends Controller
{
    use MakeArrayFromString;

    public function index()
    {
        $excluded = $this->makeArray(request('excluded'));

        return EmploymentStatus::when(
            count($excluded),
            fn (Builder $b) => $b->whereNotIn('alias', $excluded)
                ->orWhereNull('alias')
        )->get(['id', 'name', 'alias']);
    }
}