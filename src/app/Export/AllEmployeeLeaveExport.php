<?php

namespace App\Export;

use App\Helpers\Traits\DateRangeHelper;
use App\Helpers\Traits\DateTimeHelper;
use App\Models\Tenant\Leave\Leave;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AllEmployeeLeaveExport implements FromArray, WithHeadings, ShouldAutoSize
{
    use Exportable, DateTimeHelper, DateRangeHelper;


    private Collection $users;

    public function __construct(Collection $users)
    {
        $this->users = $users;
    }

    public function headings(): array
    {
        return [
            __t('name'),
            __t('duration_type'),
            __t('start_at'),
            __t('end_at'),
            __t('leave_type'),
            __t('status'),
            __t('reason_note'),
        ];
    }

    public function array(): array
    {
        return $this->users->map(function ($user) {
            return $this->getUserLeavesRows($user->leaves, $user->full_name);
        })->flatten(1)->toArray();

    }

    public function getUserLeavesRows($leaves, $name)
    {
        return $leaves->map(fn(Leave $leave) => $this->makeAttendanceRow($leave, $name));
    }

    public function makeAttendanceRow($leave, $name): array
    {

        $start_at = $this->carbon($leave->start_at)->parse()->setTimezone(request('timeZone'))->toDateTimeString();
        $end_at = $this->carbon($leave->end_at)->parse()->setTimezone(request('timeZone'))->toDateTimeString();

        return [
            $name,
            __t($leave->duration_type),
            $start_at,
            $end_at,
            $leave->type->name,
            $leave->status->translated_name,
            $this->getNote($leave->comments, 'reason-note'),
        ];

    }

    private function getNote(Collection $comments, $type)
    {
        if (!$comments->count()) return null;

        return optional($comments->where('type', $type)->sortByDesc('parent_id')->first())->comment;
    }
}