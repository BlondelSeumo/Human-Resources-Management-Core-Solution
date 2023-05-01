<?php

namespace App\Imports;


use App\Helpers\Traits\DateTimeHelper;
use App\Helpers\Traits\MakeArrayFromString;
use App\Helpers\Traits\NameSplitTrait;
use App\Models\Core\Auth\User;
use App\Repositories\Core\Status\StatusRepository;
use App\Services\Tenant\Attendance\AttendanceService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\Failure;

class AttendanceImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading, SkipsOnFailure
{
    use Importable, SkipsFailures, MakeArrayFromString, NameSplitTrait, DateTimeHelper;

    private int $rows = 1;

    public function model(array $row)
    {
        ++$this->rows;
        $in_date = $this->carbon($row['in_time'])->parse()->toDateString();
        $in_time = $this->carbon($row['in_time'], request('timeZone'))->parse()->setTimezone('UTC');
        $out_time = $this->carbon($row['out_time'], request('timeZone'))->parse()->setTimezone('UTC');
        $user = User::query()->whereHas('profile', function (Builder $builder) use ($row) {
            $builder->where('employee_id', $row['employee_id']);
        })->with('department.manager:id')->first();
        $status = resolve(StatusRepository::class)->attendanceApprove();

        try {
            DB::transaction(fn() => resolve(AttendanceService::class)
                ->setRefreshMemoization(true)
                ->setAttrs(array_merge($row, [
                    'in_date' => $in_date,
                    'in_time' => $in_time,
                    'out_time' => $out_time,
                    'review_by' => auth()->id(),
                    'added_by' => auth()->id(),
                    'status_id' => $status,
                    'note_type' => 'manual',
                ]))->validateManual()
                ->validateIfNotFuture()
                ->setModel($user)
                ->validateOwnDepartmentUser()
                ->manualAddPunch()
                ->when(true,
                    function (AttendanceService $service) use ($status) {
                        $attributes = ['status_id' => $status];
                        if (!$service->isNotFirstAttendance()) {
                            $attributes = array_merge([
                                'behavior' => $service->getUpdateBehavior()
                            ], $attributes);
                        }
                        $service->updateAttendance($attributes);
                    }
                )
            );
        } catch (\Exception $e) {

            if ($e instanceof ValidationException) {
                foreach ($e->errors() as $key => $error) {
                    $failure = new Failure(
                        $this->rows,
                        $key,
                        [$error[0]],
                        $row
                    );
                    array_push($this->failures, $failure);
                }

            } else {
                $failure = new Failure(
                    $this->rows,
                    'employee_id',
                    [$e->getMessage()],
                    $row
                );
                array_push($this->failures, $failure);
            }
        }

    }


    public array $requiredHeading = [
        "employee_id",
        "in_time",
        "out_time",
        "note",
    ];

    public function rules(): array
    {
        return [
            '*.employee_id' => ['required', 'string', 'exists:profiles,employee_id'],
            '*.in_time' => ['required', 'date_format:Y-m-d H:i:s'],
            '*.out_time' => ['required', 'date_format:Y-m-d H:i:s'],
        ];
    }

    public function batchSize(): int
    {
        return 100;
    }

    public function chunkSize(): int
    {
        return 100;
    }
}
