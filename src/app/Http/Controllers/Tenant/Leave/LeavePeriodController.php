<?php

namespace App\Http\Controllers\Tenant\Leave;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Leave\LeavePeriod;
use App\Services\Tenant\Leave\LeavePeriodService;
use Illuminate\Http\Request;

class LeavePeriodController extends Controller
{
    public function __construct(LeavePeriodService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return $this->service
            ->filters($this->filter)
            ->latest()
            ->paginate(request()->get('per_page', 10));
    }


    public function store(Request $request)
    {
        $this->service
            ->setAttributes($request->only( 'start_date', 'end_date'))
            ->validate()
            ->save();

        return created_responses('leave_period');
    }


    public function show(LeavePeriod $leavePeriod)
    {
        return $leavePeriod;
    }


    public function update(Request $request, LeavePeriod $leavePeriod)
    {
        $this->service
            ->setModel($leavePeriod)
            ->setAttributes($request->only( 'start_date', 'end_date'))
            ->validate()
            ->save();

        return updated_responses('leave_period');
    }


    public function destroy(LeavePeriod $leavePeriod)
    {
        $leavePeriod->delete();

        return deleted_responses('leave_period');
    }
}
