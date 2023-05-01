<?php

namespace App\Http\Controllers\Tenant\Employee;

use App\Filters\Tenant\AnnouncementFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Employee\AnnouncementRequest;
use App\Models\Tenant\Employee\Announcement\Announcement;
use App\Services\Tenant\Employee\AnnouncementService;
use Illuminate\Support\Facades\DB;

class AnnouncementController extends Controller
{
    public function __construct(AnnouncementService $service, AnnouncementFilter $filter)
    {
        $this->service = $service;
        $this->filter = $filter;
    }

    public function index()
    {
        return $this->service
            ->filters($this->filter)
            ->with('departments', 'createdBy')
            ->latest('id')
            ->paginate(request()->get('per_page', 10));
    }

    public function store(AnnouncementRequest $request)
    {
        DB::transaction(
            fn() => $this->service
                ->setModel(
                    $this->service
                        ->setAttributes($request->only('name', 'start_date', 'end_date', 'description'))
                        ->save()
                )
                ->assignToDepartments($request->get('departments', []))
        );

        return created_responses('announcement');
    }

    public function show(Announcement $announcement)
    {
        $announcement
            ->load([
                    'departments' => fn($b) => $b
                        ->select('departments.id', 'departments.name')
                ]
            );

        return $announcement;
    }

    public function update(Announcement $announcement, AnnouncementRequest $request)
    {
        DB::transaction(
            fn() => $this->service
                ->setAttributes($request->only('name', 'start_date', 'end_date', 'description'))
                ->setModel($announcement)
                ->update()
                ->assignToDepartments($request->get('departments', []))
        );

        return updated_responses('announcement');
    }

    public function destroy(Announcement $announcement)
    {
        $this->service
            ->setModel($announcement)
            ->assignToDepartments([])
            ->delete();

        return deleted_responses('announcement');
    }
}
