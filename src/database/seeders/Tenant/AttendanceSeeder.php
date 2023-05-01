<?php

namespace Database\Seeders\Tenant;

use App\Helpers\Traits\DateRangeHelper;
use App\Models\Core\Auth\User;
use App\Models\Tenant\Attendance\Attendance;
use App\Models\Tenant\Attendance\AttendanceDetails;
use App\Models\Tenant\Utility\Comment;
use App\Repositories\Core\Status\StatusRepository;
use App\Services\Tenant\Attendance\AttendanceService;
use Database\Seeders\Traits\DisableForeignKeys;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    use DisableForeignKeys, DateRangeHelper;

    public function run()
    {
        $this->disableForeignKeys();
        $dateRanges = $this->dateRange(now()->subMonths(1), now());
        $faker = resolve(\Faker\Generator::class);
        $status = resolve(StatusRepository::class)->attendanceApprove();
        //Comment::query()->truncate();
        AttendanceDetails::query()->truncate();
        Attendance::truncate();

        User::query()->where('status_id', resolve(StatusRepository::class)->userActive())->get()
            ->map(function (User $user) use ($dateRanges, $faker, $status) {
                foreach ($dateRanges as $key => $range) {
                    $now = $range->format('Y-m-d')." ".now()->format('H:i:s');
                    $punchOut = $range->format('Y-m-d')." ".now()->addHours(rand(5, 9))->format('H:i:s');

                    resolve(AttendanceService::class)
                        ->setModel($user)
                        ->setAttributes(['status_id' => $status, 'punch_in' => true, 'now' => $now, 'today' => $now])
                        ->setRefreshMemoization(true)
                        ->punchIn();

                    resolve(AttendanceService::class)
                        ->setModel($user)
                        ->setAttributes(['status_id' => $status, 'punch_in' => false, 'now' => $punchOut, 'today' => $punchOut])
                        ->setRefreshMemoization(true)
                        ->punchOut();
                }
            });

        $this->enableForeignKeys();
    }
}
