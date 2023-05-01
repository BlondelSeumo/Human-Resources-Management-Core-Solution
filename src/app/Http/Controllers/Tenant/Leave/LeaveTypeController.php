<?php

namespace App\Http\Controllers\Tenant\Leave;

use App\Exceptions\GeneralException;
use App\Filters\Tenant\LeaveTypeFilter;
use App\Http\Controllers\Controller;
use App\Models\Tenant\Leave\LeaveType;
use App\Services\Tenant\Leave\LeaveTypeService;
use Illuminate\Http\Request;
use function PHPUnit\Framework\throwException;

class LeaveTypeController extends Controller
{
    public function __construct(LeaveTypeService $service, LeaveTypeFilter $filter)
    {
        $this->service = $service;
        $this->filter = $filter;
    }

    public function index()
    {
        return LeaveType::filters($this->filter)
            ->latest()
            ->paginate(request()->get('per_page', 10));
    }

    public function store(Request $request)
    {
        $this->service
            ->setAttributes($request->only('name', 'type', 'amount', 'special_percentage','is_enabled','is_earning_enabled'))
            ->validate()
            ->save();

        return created_responses('leave_type');
    }


    public function show(LeaveType $leaveType)
    {
        return $leaveType;
    }

    public function update(Request $request, LeaveType $leaveType)
    {
        $this->service
            ->setModel($leaveType)
            ->setAttributes($request->only('name', 'type', 'amount', 'special_percentage','is_enabled','is_earning_enabled'))
            ->validate()
            ->validateLeaves()
            ->save();

        return updated_responses('leave_type');
    }


    public function destroy(LeaveType $leaveType)
    {
        try {
            $leaveType->delete();
        } catch (\Exception $e) {
            throw new GeneralException(__t('can_not_delete_used_leave_type'));
        }

        return deleted_responses('leave_type');
    }

}
