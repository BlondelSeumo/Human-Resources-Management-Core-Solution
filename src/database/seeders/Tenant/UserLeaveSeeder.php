<?php

namespace Database\Seeders\Tenant;

use App\Helpers\Traits\SettingHelper;
use App\Models\Core\Auth\User;
use App\Models\Tenant\Leave\LeaveType;
use App\Models\Tenant\Leave\UserLeave;
use Database\Seeders\Traits\DisableForeignKeys;
use Illuminate\Database\Seeder;

class UserLeaveSeeder extends Seeder
{
    use DisableForeignKeys, SettingHelper;

    public function run()
    {
        $this->disableForeignKeys();
        UserLeave::query()->truncate();

        $ranges = $this->leaveYear();
        $leaves = LeaveType::query()->pluck('id')->reduce(function ($leaves, $leaveType) use ($ranges) {
            return array_merge($leaves, User::query()->get()
                ->map(fn(User $user) => [
                    'user_id' => $user->id,
                    'leave_type_id' => $leaveType,
                    'amount' => rand(5, 10),
                    'start_date' => $ranges[0],
                    'end_date' => $ranges[1],
                ])->toArray()
            );
        }, []);

        UserLeave::insert($leaves);

        $this->enableForeignKeys();
    }
}
