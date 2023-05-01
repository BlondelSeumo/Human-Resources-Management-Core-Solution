<?php

namespace App\Http\Controllers\Tenant\Payroll;

use App\Http\Controllers\Controller;
use App\Services\Tenant\Payroll\PayrunService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RunDefaultPayrun extends Controller
{
    public function __construct(PayrunService $service)
    {
        $this->service = $service;
    }

    public function store()
    {
        DB::transaction(function (){
            $this->service
                ->setAttr('note', request()->get('note'))
                ->runDefaultPayrun();
        });

        return created_responses('payrun');
    }
}
