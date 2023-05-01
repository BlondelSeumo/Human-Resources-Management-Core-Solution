<?php

namespace App\Services\Restore;

use App\Helpers\Core\Traits\Memoization;
use App\Helpers\Traits\DateTimeHelper;
use App\Helpers\Traits\SettingKeyHelper;
use App\Models\Core\Auth\Role;
use App\Models\Core\Auth\User;
use App\Models\Tenant\Attendance\Attendance;
use App\Models\Tenant\Attendance\AttendanceDetails;
use App\Models\Tenant\Employee\Department;
use App\Models\Tenant\Employee\Designation;
use App\Models\Tenant\Employee\EmploymentStatus;
use App\Models\Tenant\Employee\UserContact;
use App\Models\Tenant\Leave\Leave;
use App\Models\Tenant\Utility\Comment;
use App\Models\Tenant\WorkingShift\WorkingShift;
use App\Models\Tenant\Holiday\Holiday;
use App\Models\Tenant\Leave\LeaveType;
use App\Models\Tenant\WorkingShift\WorkingShiftDetails;
use App\Repositories\Core\Status\StatusRepository;
use App\Services\Tenant\Employee\EmployeeContactService;
use App\Services\Tenant\Employee\EmployeeEmploymentStatusService;
use App\Services\Tenant\Employee\EmployeeService;
use App\Services\Tenant\TenantService;
use App\Services\Tenant\WorkingShift\WorkingShiftService;
use Carbon\Carbon;
use Database\Seeders\Traits\DisableForeignKeys;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RestoreService extends TenantService
{
    use DateTimeHelper, DisableForeignKeys, Memoization, SettingKeyHelper;

    protected $db;

    public function setDb($db): self
    {
        $this->db = $db;

        return $this;
    }

    public function runDefaultSeeder(): self
    {
        Artisan::call('migrate:fresh --seed --force');

        return $this;
    }

    public function restoreDepartments(): self
    {
        $MainDepartment = $this->db
            ->table('sveipa_hrm_org_tree')
            ->where('parent_id', '=', 0)
            ->first();

        Department::query()->whereNull('department_id')->update(['name' => $MainDepartment->title]);

        $departments = $this->db
            ->table('sveipa_hrm_org_tree as a')
            ->where('a.parent_id', '!=', 0)
            ->leftJoin('sveipa_hrm_org_tree as b', 'b.id', '=', 'a.parent_id')
            ->select('a.*', 'b.title as parent_title')
            ->get();

        foreach ($departments as $department) {
            $this->createDepartment($department->title);
        }

        foreach ($departments as $department) {
            Department::query()->where('name', $department->title)->update([
                'department_id' => Department::query()->where('name', $department->parent_title)->first()->id ?:
                    Department::query()->where('name', $MainDepartment->title)->first()->id
            ]);
        }

        return $this;
    }

    public function createDepartment($title)
    {
        $statusActive = resolve(StatusRepository::class)->departmentActive();

        return Department::query()->firstOrCreate([
            'name' => $title
        ], [
            'name' => $title,
            'tenant_id' => 1,
            'manager_id' => User::query()->oldest()->first()->id,
            'status_id' => $statusActive
        ]);
    }

    public function restoreDesignations(): self
    {
        $designations = $this->db
            ->table('sveipa_hrm_job_titles')
            ->get();

        foreach ($designations as $designation) {
            Designation::query()->firstOrCreate([
                'name' => $designation->title
            ], [
                'name' => $designation->title,
                'description' => $designation->description,
                'tenant_id' => 1
            ]);
        }

        return $this;
    }

    public function restoreEmployees($adminPass): self
    {
        $employees = $this->getEmployees();

        foreach ($employees as $employee) {
            if ($employee->email == 'risul.islam@regfire.com') {
                $user = User::query()->find(1);

                $user->update([
                    'first_name' => $employee->first_name,
                    'last_name' => $employee->last_name,
                    'is_in_employee' => 1,
                    'password' => $adminPass,
                    'email' => $employee->email,
                    'status_id' => resolve(StatusRepository::class)->userActive()
                ]);

            } else {
                $user = User::query()->firstOrCreate([
                    'email' => $employee->email ?: $this->makeEmail($employee)
                ], [
                    'first_name' => $employee->first_name,
                    'last_name' => $employee->last_name,
                    'password' => Hash::make(Str::random(8)),
                    //'password' => Hash::make(123456),
                    'email' => $employee->email ?: $this->makeEmail($employee),
                    'is_in_employee' => 1,
                    'status_id' => resolve(StatusRepository::class)->userActive()
                ]);
            }

            $this->saveProfile($user, $employee)
                ->saveDepartment($user, $employee)
                ->saveDesignation($user, $employee)
                ->assignToRoles($user, 'employee')
                ->saveAddressDetails($user, $employee)
                ->saveEmergencyContact($user, $employee)
                ->saveEmploymentStatus($user, $employee);
        }

        return $this;
    }

    public function makeEmail($employee): string
    {
        return Str::snake($employee->first_name . ' ' . $employee->last_name) . '@demo.com';
    }

    public function saveEmergencyContact($user, $employee): self
    {
        $emergencyContacts = $this->db
            ->table('sveipa_hrm_emergency_contacts')
            ->where('employee_id', '=', $employee->id)
            ->select(
                'sveipa_hrm_emergency_contacts.name as emergency_contact_name',
                'sveipa_hrm_emergency_contacts.relationship as emergency_contact_relationship',
                'sveipa_hrm_emergency_contacts.mobile as emergency_contact_mobile'
            )->get()->toArray();

        foreach ($emergencyContacts as $emergencyContact) {
            $user->contacts()->save(new UserContact([
                'key' => 'emergency_contacts',
                'value' => json_encode([
                    'name' => $emergencyContact->emergency_contact_name,
                    'relationship' => $emergencyContact->emergency_contact_relationship,
                    'phone_number' => $emergencyContact->emergency_contact_mobile,
                ])
            ]));
        }

        return $this;
    }

    public function saveAddressDetails($user, $details): self
    {
        if ($details->permanent_address || $details->city || $details->zip || $details->country || $details->state) {
            resolve(EmployeeContactService::class)
                ->setAttributes([
                    'details' => $details->current_address,
                    'city' => $details->city,
                    'zip_code' => $details->zip,
                    'country' => $details->country,
                    'state' => $details->state,
                    'type' => 'present_address',
                ])->setModel($user)
                ->updateAddress();
        }

        if ($details->permanent_address) {
            resolve(EmployeeContactService::class)
                ->setAttributes([
                    'details' => $details->permanent_address,
                    'type' => 'permanent_address',
                ])->setModel($user)
                ->updateAddress();
        }

        return $this;
    }

    public function assignToRoles($user, $role): self
    {
        $role = $user->full_name == 'Shakti Kumar Das' || $user->email == 'risul.islam@regfire.com' ? 'admin' : $role;

        resolve(EmployeeService::class)
            ->setModel($user)
            ->setAttr('roles', Role::query()->where('alias', $role)->pluck('id')->toArray())
            ->assignRolesFromAttribute();

        return $this;
    }

    public function saveProfile($user, $details): self
    {
        $attributes = [
            'user_id' => $user->id,
            'employee_id' => $details->username,
            'phone_number' => $details->phone_number,
        ];
        $joiningDate = $this->carbon($details->joining_date)->parse()->toDateString();

        if ($details->joining_date != '' && $joiningDate == $details->joining_date) {
            $attributes = array_merge($attributes, ['joining_date' => $joiningDate]);
        }

        $user->profile()->updateOrCreate([
            'user_id' => $user->id
        ], $attributes);

        return $this;
    }

    public function saveDepartment($user, $details): self
    {
        $departmentId = $details->department ?
            Department::query()->where('name', $details->department)->first()->id :
            $this->getUnknownDepartment()->id;

        resolve(EmployeeService::class)
            ->setModel($user)
            ->setAttr('department_id', $departmentId)
            ->assignToDepartment();

        return $this;
    }

    public function getUnknownDepartment()
    {
        return $this->memoize('unknown-department', function () {
            $statusInactive = resolve(StatusRepository::class)->departmentInactive();

            return Department::query()->firstOrCreate([
                'name' => 'unknown'
            ], [
                'name' => 'unknown',
                'description' => '',
                'department_id' => Department::query()->whereNull('department_id')->first()->id,
                'manager_id' => User::query()->oldest()->first()->id,
                'tenant_id' => 1,
                'status_id' => $statusInactive
            ]);
        }, $this->refreshMemoization);
    }

    public function getUnknownDesignation()
    {
        return $this->memoize('unknown-designation', function () {
            return Designation::query()->firstOrCreate([
                'name' => 'unknown'
            ], [
                'name' => 'unknown',
                'description' => '',
                'tenant_id' => 1
            ]);
        }, $this->refreshMemoization);
    }

    public function saveDesignation($user, $details): self
    {
        $designationId = $details->designation ?
            Designation::query()->where('name', $details->designation)->first()->id :
            $this->getUnknownDesignation()->id;

        resolve(EmployeeService::class)
            ->setModel($user)
            ->setAttr('designation_id', $designationId)
            ->assignToDesignation();

        return $this;
    }

    public function saveEmploymentStatus($user, $details): self
    {
        $status = $details->employment_status ? 'probation' : 'terminated';

        if (strpos(strtolower($details->employment_status), 'permanent') !== false ||
            strpos(strtolower($details->employment_status), 'parmanent') !== false) {
            $status = 'permanent';
        }

        if ($details->is_terminated) {
            $status = 'terminated';
        }

        if ($status == 'terminated') {
            $terminateStatus = EmploymentStatus::getByAlias('terminated');

            resolve(EmployeeEmploymentStatusService::class)
                ->setModel($terminateStatus)
                ->setAttr('description', $details->termination_note)
                ->changeStatus($user, 'inactive');
        } else {
            resolve(EmployeeService::class)
                ->setModel($user)
                ->setAttr('employment_status_id', EmploymentStatus::query()->where('name', $status)->first()->id)
                ->assignEmploymentStatus();
        }

        return $this;
    }

    public function getEmployees()
    {
        return $this->db
            ->table('sveipa_people')
            //->where('sveipa_people.email', '!=', '')
            ->leftJoin('sveipa_employees',
                'sveipa_employees.person_id', '=', 'sveipa_people.person_id')
            ->leftJoin('sveipa_hrm_employee_job_info',
                'sveipa_hrm_employee_job_info.employee_id', '=', 'sveipa_people.person_id')
            ->leftJoin('sveipa_hrm_job_titles',
                'sveipa_hrm_employee_job_info.jobtitle', '=', 'sveipa_hrm_job_titles.id')
            ->leftJoin('sveipa_hrm_employment_status',
                'sveipa_hrm_employee_job_info.employment_status', '=', 'sveipa_hrm_employment_status.id')
            ->leftJoin('sveipa_hrm_org_tree',
                'sveipa_hrm_employee_job_info.department', '=', 'sveipa_hrm_org_tree.id')
            ->select(
                'sveipa_people.person_id as id',
                'sveipa_employees.username',
                'sveipa_people.first_name',
                'sveipa_people.last_name',
                'sveipa_people.phone_number',
                'sveipa_people.email',
                'sveipa_people.address_1 as current_address',
                'sveipa_people.address_2 as permanent_address',
                'sveipa_people.city',
                'sveipa_people.zip',
                'sveipa_people.state',
                'sveipa_people.country',
                'sveipa_hrm_employee_job_info.joining_date',
                'sveipa_hrm_job_titles.title as designation',
                'sveipa_hrm_org_tree.title as department',
                'sveipa_hrm_employment_status.status as employment_status',
                'sveipa_hrm_employee_job_info.is_terminated',
                'sveipa_hrm_employee_job_info.termination_date',
                'sveipa_hrm_employee_job_info.termination_reason',
                'sveipa_hrm_employee_job_info.termination_note'
            )->get()->toArray();
    }

    public function restoreWorkShift($default_work_shift): self
    {
        WorkingShift::query()->create($default_work_shift->toArray());
        resolve(WorkingShiftService::class)
            ->setModel($default_work_shift)
            ->setWorkingShiftDetails($default_work_shift->details->toArray())
            ->saveDetails();

        $workShifts = $this->db
            ->table('sveipa_hrm_workshifts')
            ->leftJoin('sveipa_hrm_org_tree', 'sveipa_hrm_org_tree.id', '=', 'sveipa_hrm_workshifts.department')
            ->select('sveipa_hrm_workshifts.*',
                'sveipa_hrm_org_tree.title as department_name'
            )->get()->toArray();

        foreach ($workShifts as $workShift) {
            $workShift->details = $this->makeWorkShiftDetails($workShift);

            $workingShift = WorkingShift::query()->firstOrCreate([
                'name' => $workShift->title
            ], [
                'name' => $workShift->title,
                'start_date' => $workShift->start_date,
                'end_date' => $workShift->end_date,
                'type' => $this->workShiftScheduleName($workShift),
                'tenant_id' => 1
            ]);

            resolve(WorkingShiftService::class)
                ->setModel($workingShift)
                ->setWorkingShiftDetails($workShift->details)
                ->saveDetails();
        }

        return $this;
    }

    public function workShiftScheduleName($workShift): string
    {
        $details = $this->db
            ->table('sveipa_hrm_shiftweeks')
            ->where('workshift_id', '=', $workShift->id)
            ->where('start_time', '!=', '')
            ->where('start_time', '!=', 0)
            ->select('start_time', 'end_time')
            ->get();

        $details = json_decode(json_encode($details), true);

        if (count($details) <= 1) {
            return 'regular';
        }

        return count(array_unique($details, SORT_REGULAR)) == 1 ? 'regular' : 'scheduled';
    }

    public function makeWorkShiftDetails($workShift)
    {
        $details = $this->db
            ->table('sveipa_hrm_shiftweeks')
            ->where('workshift_id', '=', $workShift->id)
            ->get();

        return $details->map(fn($item) => [
            'start_at' => $item->start_time ? $this->convertLocalToUtc($item->start_time) : null,
            'is_weekend' => $item->start_time ? 0 : 1,
            'end_at' => $item->end_time ? $this->convertLocalToUtc($item->end_time) : null,
            'weekday' => strtolower($this->carbon($item->day_name)->parse()->shortDayName),
        ])->toArray();
    }

    public function convertLocalToUtc($dateTime)
    {
        return $this->carbon($dateTime, 'asia/dhaka')
            ->parse()
            ->setTimezone('UTC')
            ->format('H:i');
    }

    public function restoreHolidays(): self
    {
        $holidays = $this->db
            ->table('sveipa_hrm_holidays')
            ->where('status', '=', 'Active')
            ->get();
        foreach ($holidays as $holiday) {
            $value = Holiday::query()->firstOrCreate([
                'name' => $holiday->title
            ], [
                'name' => $holiday->title,
                'start_date' => $holiday->start_date,
                'end_date' => $holiday->end_date,
                'description' => '',
                'tenant_id' => 1,
                'repeats_annually' => $holiday->repeat_annually == 'Yes' ? 1 : 0,
            ]);

            if ($value->repeats_annually && Carbon::parse($value->start_date)->year < now()->year) {
                $newHoliday = $value->replicate()->fill([
                    'start_date' => Carbon::parse($value->start_date)->setYear(now()->year),
                    'end_date' => Carbon::parse($value->end_date)->setYear(now()->year),
                ]);
                $newHoliday->save();
            }
        }
        return $this;
    }

    public function restoreLeaveType(): self
    {
        $this->disableForeignKeys();
        LeaveType::query()->truncate();
        $this->enableForeignKeys();

        $leave_types = $this->db
            ->table('sveipa_hrm_leave_types')
            ->get();
        foreach ($leave_types as $leave_type) {
            LeaveType::query()->firstOrCreate([
                'name' => $leave_type->title
            ], [
                'name' => $leave_type->title,
                'alias' => Str::snake(strtolower($leave_type->title)),
                'type' => strtolower($leave_type->type),
                'amount' => 5,
                'special_percentage' => 0.0,
                'is_enabled' => 1,
                'is_earning_enabled' => 0,
                'tenant_id' => 1,
            ]);
        }

        return $this;
    }

    public function restoreAttendances($skip, $take): self
    {
//        $this->db->table('sveipa_hrm_attendances')->chunkById(1000, function ($attendances){
//            foreach ($attendances as $attendance){
//                $this->saveAttendance($attendance);
//            }
//        });

        $attendances = $this->db
            ->table('sveipa_hrm_attendances')
            ->skip($skip)
            ->take($take)
            ->get()
            ->toarray();

        foreach ($attendances as $attendance) {
            $this->saveAttendance($attendance);
        }

        return $this;
    }

    public function saveAttendance($attendance): self
    {
        $employee = $this->getEmployee($attendance->employee_id);
        $inTime = $this->carbon($attendance->in_time, 'asia/dhaka')->parse()->addHours($attendance->in_offset);
        $outTime = $this->carbon($attendance->out_time, 'asia/dhaka')->parse()->addHours($attendance->out_offset);

        if ($employee && !$inTime->isBefore('2000-01-01')) {
            $logs = $this->getAttendanceLog($attendance->id);
            $workShift = $this->getWorkingShift($inTime) ?: WorkingShift::getDefault();

            $newAttendance = $employee->attendances()
                ->whereDate('in_date', $inTime)
                ->firstOr(fn() => $this->createAttendance($employee, $inTime, $workShift));

            $newAttendance->details()->save(new AttendanceDetails([
                'in_time' => $inTime->setTimezone('UTC'),
                'out_time' => $attendance->status == 1 ? null : $outTime->setTimezone('UTC'),
                'status_id' => $this->getAttendanceApproveStatus(),
                'added_by' => $logs ? $employee->id : null,
                'review_by' => $logs ? 1 : null
            ]));
        }

        return $this;
    }

    public function getAttendanceApproveStatus()
    {
        return $this->memoize('attendance-approve-status', function () {
            return resolve(StatusRepository::class)->attendanceApprove();
        }, $this->refreshMemoization);

    }

    public function createAttendance($employee, $inTime, $workingShift)
    {
        $status = $this->getAttendanceApproveStatus();

        return $employee->attendances()
            ->save(new Attendance([
                'in_date' => $inTime,
                'status_id' => $status,
                'working_shift_id' => $workingShift->id,
                'behavior' => $this->getAttendanceBehavior($workingShift, $inTime),
                'tenant_id' => 1,
            ]));
    }

    public function getAttendanceBehavior($workingShift, Carbon $date): string
    {
        $workingShiftDetails = WorkingShiftDetails::query()->whereWorkingShiftId($workingShift->id)
            ->where('weekday', $this->carbon($date)->toDayInLowerCase())
            ->first();

        $setting = $this->getSettingFromKey('attendance')('punch_in_time_tolerance');

        $setting = (int)$setting;

        $workShiftTime = $this->carbon($date->toDateString() . ' ' . $workingShiftDetails->start_at)->parse();

        $nowDate = clone $date;
        $nowDate = $nowDate->setTimezone('UTC');

        if ($nowDate->isBefore($workShiftTime)) {
            return 'early';
        }

        if ($nowDate->isAfter($workShiftTime->addMinutes($setting))) {
            return 'late';
        }

        return 'regular';
    }

    public function getEmployee($employeeId)
    {
        $employee = $this->db
            ->table('sveipa_people')
            ->where('person_id', '=', $employeeId)
            ->select('sveipa_people.first_name', 'sveipa_people.last_name', 'sveipa_people.email')
            ->first();

        return User::query()->where('email', $employee->email ?: $this->makeEmail($employee))->first();
    }

    public function getAttendanceLog($attendanceId)
    {
        return $this->db
            ->table('sveipa_audit_log')
            ->where('row_id', '=', $attendanceId)
            ->where('task', '=', 'attendance')
            ->exists();
    }

    public function getWorkingShift($date)
    {
        return WorkingShift::query()
            ->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)
            ->first();
    }

    public function restoreLeaves(): self
    {
        //$leaves = $this->db->table('sveipa_hrm_leaves')->limit(100)->get()->toArray();
        $leaves = $this->db->table('sveipa_hrm_leaves')->get()->toArray();

        foreach ($leaves as $leave) {
            $this->saveLeave($leave);
        }

        return $this;
    }

    public function saveLeave($leave): self
    {
        $workingShift = $this->getWorkingShift($leave->from_date) ?: WorkingShift::getDefault();
        $workingShiftDetails = WorkingShiftDetails::query()->whereWorkingShiftId($workingShift->id)
            ->where('weekday', $this->carbon($leave->from_date)->toDayInLowerCase())
            ->first();

        $duration_type = $this->getDurationType($leave->total_days);
        $statusId = $this->getLeaveStatus($leave->status);

        $date = $leave->from_date;

        $employee = $this->getEmployee($leave->employee_id);

        $leave_type = $this->getLeaveType($leave->leave_type)->id;

        if ($leave->total_days < 1) {
            $start_at = $this->carbon($leave->from_date . ' ' . $leave->from_time, 'asia/dhaka')
                ->parse()->setTimezone('UTC');
            $end_at = $this->carbon($leave->to_date . ' ' . $leave->to_time, 'asia/dhaka')
                ->parse()->setTimezone('UTC');
        } else {
            $start_at = $this->carbon($leave->from_date . ' ' . $workingShiftDetails->start_at)->parse();
            $end_at = $this->carbon($leave->to_date . ' ' . $workingShiftDetails->end_at)->parse();
        }
        $newLeave = new Leave([
            'status_id' => $statusId,
            'working_shift_details_id' => $workingShiftDetails->id,
            'date' => $date,
            'start_at' => $start_at,
            'end_at' => $end_at,
            'leave_type_id' => $leave_type,
            'duration_type' => $duration_type,
            'tenant_id' => 1,
        ]);

        $employee->leaves()->save($newLeave);

        $newLeave->comments()->save(new Comment([
            'user_id' => $employee->id,
            'type' => 'reason-note',
            'comment' => $leave->comment,
        ]));
        return $this;
    }

    public function getLeaveType($leaveType)
    {
        $leaveType = $this->db->table('sveipa_hrm_leave_types')->where('id', '=', $leaveType)->select('title')->first();

        return LeaveType::query()->where('alias', Str::snake($leaveType->title))->first();
    }

    public function getLeaveStatus($status)
    {
        if ($status == 'Approved') {
            return resolve(StatusRepository::class)->leaveApproved();
        }

        return resolve(StatusRepository::class)->leaveCanceled();
    }

    public function getDurationType($days): string
    {
        if ($days > 1) {
            return 'multi_day';
        }

        if ($days < 1) {
            return 'hours';
        }

        return 'single_day';
    }
}
