<?php

namespace App\Http\Controllers\Tenant\Employee;

use App\Exceptions\GeneralException;
use App\Filters\Tenant\EmploymentStatusFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Employee\EmploymentStatusRequest;
use App\Models\Tenant\Employee\EmploymentStatus;
use App\Services\Tenant\Employee\EmploymentStatusService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EmploymentStatusController extends Controller
{
    public function __construct(EmploymentStatusService $service, EmploymentStatusFilter $filter)
    {
        $this->filter = $filter;
        $this->service = $service;
    }

    public function index()
    {
        return $this->service
            ->filters($this->filter)
            ->latest('id')
            ->paginate(request()->get('per_page', 10));
    }

    public function store(EmploymentStatusRequest $request)
    {
        $status = $this->service
            ->setAttrs($request->only('name', 'class', 'description'))
            ->setAttr('alias', Str::slug($request->get('name')))
            ->save();

        return created_responses('employment_status', ['employment_status' => $status]);
    }

    public function show(EmploymentStatus $employmentStatus)
    {
        return $employmentStatus;
    }

    public function update(Request $request, EmploymentStatus $employmentStatus)
    {
        $this->service
            ->setModel($employmentStatus)
            ->setAttrs($request->only('name', 'class', 'description'))
            ->setAttr('alias', Str::slug($request->get('name')))
            ->validations()
            ->update();

        return updated_responses('employment_status', ['employment_status' => $employmentStatus]);
    }

    public function destroy(EmploymentStatus $employmentStatus)
    {
        if ($employmentStatus->is_default) {
            throw new GeneralException(__t('action_not_allowed'));
        } elseif ($employmentStatus->employees->count()) {
            throw new GeneralException(__t('cant_delete_employment_status'));
        }

        $employmentStatus->delete();

        return deleted_responses('employment_status');
    }
}
