<?php

namespace App\Http\Controllers\Tenant\Holiday;

use App\Filters\Tenant\HolidayFilter;
use App\Helpers\Traits\DateTimeHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Holiday\HolidayRequest;
use App\Models\Tenant\Holiday\Holiday;
use App\Services\Tenant\Holiday\HolidayService;
use Illuminate\Http\Request;


class HolidayController extends Controller
{
    use DateTimeHelper;

    public function __construct(HolidayService $service, HolidayFilter $filter)
    {
        $this->service = $service;
        $this->filter = $filter;
    }

    public function index()
    {
        return $this->service
            ->filters($this->filter)
            ->with('departments:id,name',)
            ->oldest('start_date')
            ->when(request()->has('view_type') && request()->has('view_type') == 'calender',
                fn ($builder) =>$builder->paginate(Holiday::query()->count()),
                fn ($builder) =>$builder
                    ->when(!request()->get('time_period') ||
                        !json_decode(htmlspecialchars_decode(request()->get('time_period')), true),
                        fn($query) => $query->whereYear('start_date', nowFromApp()->year),
                        fn($builder) => $this->service->timePeriod($builder, request()->get('time_period'))
                    )->paginate(request()->get('per_page', 10))
            );
    }

    public function store(HolidayRequest $request)
    {
        /** @var Holiday $holiday */
        $holiday = $this->service
            ->save($request->only('name', 'start_date', 'end_date', 'description', 'repeats_annually', 'tenant_id'));

        $holiday->departments()->sync($request->get('departments'));

        return created_responses('holiday');
    }

    public function show(Holiday $holiday)
    {
        return $holiday->load('departments:id');
    }

    public function update(Holiday $holiday, Request $request)
    {
        $this->service
            ->setAttributes(request()->all())
            ->validateForUpdate($holiday)
            ->validateUpdateRequest($holiday);

        $startDate = $this->carbon($holiday->start_date)->parse();

        $attributes = request()->only('name', 'start_date', 'end_date', 'description', 'repeats_annually');

        if ($startDate->isPast() && $startDate->isCurrentYear())
        {
            $attributes = request()->only('repeats_annually');
        }
        $holiday->update($attributes);

        $holiday->departments()->sync(request()->get('departments'));

        return created_responses('holiday');
    }

    public function destroy(Holiday $holiday)
    {
        $this->service->validateForDelete($holiday);

        $holiday->departments()->sync([]);

        $holiday->delete();
        
        return deleted_responses('holiday');
    }
}
