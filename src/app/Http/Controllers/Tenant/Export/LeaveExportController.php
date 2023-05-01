<?php

namespace App\Http\Controllers\Tenant\Export;

use App\Export\AllEmployeeLeaveExport;
use App\Export\EmployeeLeaveExport;
use App\Filters\Tenant\Helper\UserAccessFilter;
use App\Filters\Tenant\LeaveSummeryFilter;
use App\Helpers\Traits\DateRangeHelper;
use App\Helpers\Traits\SettingHelper;
use App\Helpers\Traits\SettingKeyHelper;
use App\Http\Controllers\Controller;
use App\Models\Core\Auth\User;
use App\Models\Tenant\Leave\Leave;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Excel;

class LeaveExportController extends Controller
{
    use DateRangeHelper, SettingKeyHelper, SettingHelper;

    public UserAccessFilter $accessFilter;

    public function __construct(
        LeaveSummeryFilter $filter,
        UserAccessFilter   $accessFilter,
    )
    {
        $this->filter = $filter;
        $this->accessFilter = $accessFilter;
    }

    public function exportEmployeeLeave(User $employee)
    {
        $within = request()->get('within');
        $month = $within ?: request('month_number') + 1;
        $ranges = $this->convertRangesToStringFormat($this->getStartAndEndOf($month, request()->get('year')));
        $ranges = count($ranges) == 1 ? [$ranges[0], $ranges[0]] : $ranges;

        if ($within === 'thisYear') {
            $ranges = $this->leaveYear();
        }

        $leaves = Leave::filters($this->filter)
            ->where('user_id', $employee->id)
            ->with([
                'status:id,name,class',
                'type:id,name',
                'lastReview',
                'lastReview.department:id,manager_id',
                'attachments',
                'comments' => fn(MorphMany $many) => $many->orderBy('parent_id', 'DESC')
                    ->select('id', 'commentable_type', 'commentable_id', 'user_id', 'type', 'comment', 'parent_id'),
            ])
            ->where(function (Builder $builder) use ($ranges) {
                $builder->whereBetween(DB::raw('DATE(start_at)'), $ranges)
                    ->orWhereBetween(DB::raw('DATE(end_at)'), $ranges);
            })->latest('date')
            ->get();


        $file_name = Str::of($employee->full_name)->kebab() . '-leaves-' . Str::of($within ?: \request('month'))->kebab() . '.xlsx';

        return (new EmployeeLeaveExport($leaves))->download($file_name, Excel::XLSX);

    }

    public function exportAllEmployeeLeave()
    {

        $within = request()->get('within');
        $month = $within ?: request('month_number') + 1;
        $ranges = $this->convertRangesToStringFormat($this->getStartAndEndOf($month, request()->get('year')));
        $ranges = count($ranges) == 1 ? [$ranges[0], $ranges[0]] : $ranges;

        if ($within === 'thisYear') {
            $ranges = $this->leaveYear();
        }

        $users = User::filters($this->accessFilter)
            ->select(['id', 'first_name', 'last_name'])
            ->whereHas('leaves', function (Builder $builder) use ($ranges) {
                $builder->whereBetween(DB::raw('DATE(start_at)'), $ranges)
                    ->orWhereBetween(DB::raw('DATE(end_at)'), $ranges);
            })
            ->with([
                'leaves' => function (HasMany $builder) use ($ranges) {
                    $builder->filters($this->filter)
                        ->where(function (Builder $builder) use ($ranges) {
                            $builder->whereBetween(DB::raw('DATE(start_at)'), $ranges)
                                ->orWhereBetween(DB::raw('DATE(end_at)'), $ranges);
                        });
                },
                'leaves.status:id,name,class',
                'leaves.type:id,name',
                'leaves.lastReview',
                'leaves.lastReview.department:id,manager_id',
                'leaves.attachments',
                'leaves.comments' => fn(MorphMany $many) => $many->orderBy('parent_id', 'DESC')
                    ->select('id', 'commentable_type', 'commentable_id', 'user_id', 'type', 'comment', 'parent_id'),
            ])->get();

        $file_name = 'all-employees-leaves-' . Str::of($within ?: \request('month'))->kebab() . '.xlsx';

        return (new AllEmployeeLeaveExport($users))->download($file_name, Excel::XLSX);

    }

}
