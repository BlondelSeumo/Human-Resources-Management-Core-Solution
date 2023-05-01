<?php

namespace Database\Seeders\Tenant;

use App\Helpers\Traits\DateRangeHelper;
use App\Models\Core\Auth\User;
use App\Repositories\Core\Status\StatusRepository;
use App\Services\Tenant\Attendance\AttendanceService;
use Carbon\CarbonInterval;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use DatePeriod;

class RequestAttendanceSeeder extends Seeder
{
    use DateRangeHelper;

    public function run()
    {
        $dateRanges = $this->dateRange(now()->subMonths(3)->subDays(5), now()->subMonths(3));
        $faker = resolve(\Faker\Generator::class);
        $status = resolve(StatusRepository::class)->attendancePending();

        User::where('id', '!=', 1)->where('status_id', resolve(StatusRepository::class)->userActive())->get()
            ->map(function (User $user) use ($dateRanges, $faker, $status) {
                foreach ($dateRanges as $key => $range) {
                    $now = $range->format('Y-m-d')." ".now()->format('H:i:s');
                    $punchOut = $range->format('Y-m-d')." ".now()->addHours(9)->format('H:i:s');

                    resolve(AttendanceService::class)
                        ->setRefreshMemoization(true)
                        ->setModel($user)
                        ->setAttributes([
                            'status_id' => $status,
                            'in_date' => $range->format('Y-m-d'),
                            'in_time' => $now,
                            'out_time' => $punchOut,
                            'note' => $faker->sentence(10),
                            'note_type' => 'request',
                            'note_user_id' => $user->id,
                        ])->manualAddPunchForSeeder();
                }
            });
    }
}
