<?php

namespace App\Http\Controllers\Tenant\Employee;

use App\Exceptions\GeneralException;
use App\Filters\Tenant\DesignationsFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Employee\DesignationRequest;
use App\Models\Tenant\Employee\Designation;
use App\Services\Tenant\Employee\DesignationService;

class DesignationController extends Controller
{
    public function __construct(DesignationService $service, DesignationsFilter $filter)
    {
        $this->service = $service;
        $this->filter = $filter;
    }

    public function index()
    {
        return $this->service
            ->filters($this->filter)
            ->withCount('users')
            ->latest()
            ->paginate(
                request()->get('per_page', 10)
            );
    }

    public function store(DesignationRequest $request)
    {
        $this->service->save(
            $request->only('name', 'description', 'tenant_id')
        );

        return created_responses('designation');
    }

    public function show(Designation $designation)
    {
        return $designation;
    }

    public function update(Designation $designation, DesignationRequest $request)
    {
        $designation->update(
            request()->only('name', 'description', 'tenant_id')
        );

        return updated_responses('designation');
    }

    public function destroy(Designation $designation)
    {
        if ($designation->is_default) {
            throw new GeneralException(__t('action_not_allowed'));
        } elseif ($designation->users->count()) {
            throw new GeneralException(__t('cant_delete_designation'));
        }

        $designation->delete();

        return deleted_responses('designation');
    }

}
