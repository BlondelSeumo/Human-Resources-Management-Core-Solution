<?php

namespace App\Export;

use App\Helpers\Traits\DateRangeHelper;
use App\Helpers\Traits\DateTimeHelper;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EmployeeLeaveExport implements FromArray, WithHeadings, ShouldAutoSize
{
    use Exportable, DateTimeHelper, DateRangeHelper;


    private Collection $leaves;

    public function __construct(Collection $leaves)
    {
        $this->leaves = $leaves;
    }

    public function headings(): array
    {
        return [
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
        return $this->leaves->map(function ($leave) {
            return $this->makeAttendanceRow($leave);
        })->toArray();

    }

    public function makeAttendanceRow($leave): array
    {
        $start_at = $this->carbon($leave->start_at)->parse()->setTimezone(request('timeZone'))->toDateTimeString();
        $end_at = $this->carbon($leave->end_at)->parse()->setTimezone(request('timeZone'))->toDateTimeString();

        return [
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