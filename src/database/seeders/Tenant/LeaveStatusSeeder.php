<?php

namespace Database\Seeders\Tenant;

use App\Models\Core\Auth\User;
use App\Models\Tenant\Leave\Leave;
use App\Models\Tenant\Leave\LeaveStatus;
use App\Models\Tenant\Utility\Comment;
use App\Repositories\Core\Status\StatusRepository;
use Database\Seeders\Traits\DisableForeignKeys;
use Illuminate\Database\Seeder;

class LeaveStatusSeeder extends Seeder
{
    use DisableForeignKeys;

    public function run()
    {
        $this->disableForeignKeys();
        LeaveStatus::query()->truncate();
        $users = User::query()->pluck('id')->toArray();
        $status = resolve(StatusRepository::class)->leaveApprovedRejectedCanceled();
        $faker = resolve(\Faker\Generator::class);


        Leave::query()->get()
            ->map(function ($leave) use( $status, $users){
                $leave->setStatus(
                    $status[array_rand($status)],
                    $users[array_rand(array_filter($users, fn ($user) => $leave->user_id != $user))]
                );
            });
//        LeaveStatus::query()->get()
//            ->map(function ($leaveStatus) use( $status, $users, $faker){
//                $leaveStatus->comments()->save(new Comment([
//                    'user_id' => 1,
//                    'type' => 'response-note',
//                    'comment' => $faker->sentence(10)
//                ]));
//            });

        $this->enableForeignKeys();
    }
}
