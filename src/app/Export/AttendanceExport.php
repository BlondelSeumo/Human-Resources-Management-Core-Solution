<?php

namespace App\Export;

use App\Helpers\Traits\DateRangeHelper;
use App\Helpers\Traits\DateTimeHelper;
use App\Models\Tenant\Attendance\Attendance;
use App\Models\Tenant\Attendance\AttendanceDetails;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AttendanceExport implements FromArray, WithHeadings, ShouldAutoSize
{
    use Exportable, DateTimeHelper, DateRangeHelper;


    private Collection $attendances;
    private bool $daily_log;
    private array $summery;

    public function __construct(Collection $attendances, $daily_log = false, $summery = [])
    {
        $this->attendances = $attendances;
        $this->daily_log = $daily_log;
        $this->summery = $summery;
    }

    public function headings(): array
    {
        return $this->daily_log ? [
            __t('name'),
            __t('department'),
            __t('punch_in'),
            __t('in_ip'),
            __t('in_note'),
            __t('punch_out'),
            __t('out_ip'),
            __t('out_note'),
            __t('total_hours'),
            __t('behavior'),
            __t('type'),
            __t('request_note'),
        ] : [
            __t('date'),
            __t('punch_in'),
            __t('in_ip'),
            __t('in_note'),
            __t('punch_out'),
            __t('out_ip'),
            __t('out_note'),
            __t('total_hours'),
            __t('behavior'),
            __t('type'),
            __t('request_note'),
        ];
    }

    public function array(): array
    {
        $attendances = $this->attendances->map(function (Attendance $attendance) {
            return $this->makeAttendanceRow($attendance);
        })->flatten(1)->toArray();

        if ($this->daily_log) {
            return $attendances;
        }
        return array_merge($attendances, [
            [[]],
            [
                __t('total_scheduled'),
                $this->convertSecondsToHoursMinutes($this->summery['total_scheduled']),
                __t('paid_leave'),
                $this->convertSecondsToHoursMinutes($this->summery['paid_leave']),
                __t('active_hour'),
                $this->convertSecondsToHoursMinutes($this->summery['total_worked']) . " ({$this->summery['worked_in_hour']} h)",
                __t('balance'),
                $this->convertSecondsToHoursMinutes($this->summery['balance']) . " ({$this->summery['balance_in_hour']} h)",

            ]
        ]);
    }

    public function makeAttendanceRow($attendance): array
    {
        return $attendance->details->map(function (AttendanceDetails $attendanceDetails) use ($attendance) {
            $in_time = $this->carbon($attendanceDetails->in_time)->parse()->setTimezone(request('timeZone'))->toTimeString();
            $out_time = $attendanceDetails->out_time ?
                $this->carbon($attendanceDetails->out_time)->parse()->setTimezone(request('timeZone'))->toTimeString()
                : __t('not_yet');
            $total_hours = $attendanceDetails->out_time ?
                $this->convertSecondsToHoursMinutes(
                    $this->carbon($attendanceDetails->in_time)->parse()->diffInSeconds($attendanceDetails->out_time)
                )
                : '00:00';
            return $this->daily_log ? [
                $attendance->user->full_name,
                $attendance->user->department->name,
                $in_time,
                json_decode($attendanceDetails->in_ip_data)->ip ?? '',
                $this->getNote($attendanceDetails->comments, 'in-note'),
                $out_time,
                json_decode($attendanceDetails->out_ip_data)->ip ?? '',
                $this->getNote($attendanceDetails->comments, 'out-note'),
                $total_hours,
                $attendance->behavior,
                $attendanceDetails->review_by || $attendanceDetails->added_by ? __t('manual') : __t('auto'),
                $this->getNote($attendanceDetails->comments, 'request'),
            ] : [
                $this->carbon($attendance->in_date)->parse()->setTimezone(request('timeZone'))->format('d-m-Y'),
                $in_time,
                json_decode($attendanceDetails->in_ip_data)->ip ?? '',
                $this->getNote($attendanceDetails->comments, 'in-note'),
                $out_time,
                json_decode($attendanceDetails->out_ip_data)->ip ?? '',
                $this->getNote($attendanceDetails->comments, 'out-note'),
                $total_hours,
                $attendance->behavior,
                $attendanceDetails->review_by || $attendanceDetails->added_by ? __t('manual') : __t('auto'),
                $this->getNote($attendanceDetails->comments, 'request'),
            ];
        })->toArray();

    }

    private function getNote(Collection $comments, $type)
    {
        if (!$comments->count()) return null;

        return optional($comments->where('type', $type)->sortByDesc('parent_id')->first())->comment;
    }
}