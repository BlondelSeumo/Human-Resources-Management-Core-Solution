<?php

use App\Models\Core\Auth\User;
use App\Models\Tenant\Leave\Leave;
use App\Models\Tenant\Leave\LeaveType;
use App\Repositories\Core\Status\StatusRepository;
use Faker\Generator as Faker;

$factory->define(Leave::class, function (Faker $faker) {
    return [
        'user_id' => $faker->randomElement(User::query()
            ->where('status_id', resolve(StatusRepository::class)->userActive())->pluck('id')->toArray()),
        'assigned_by' => null,
        'leave_type_id' => $faker->randomElement(LeaveType::pluck('id')->toArray()),
        'status_id' => $faker->randomElement(resolve(StatusRepository::class)
            ->statuses('leave')
            ->whereIn('name', ['status_approved', 'status_pending'])
            ->pluck('id')->toArray()),
        'working_shift_details_id' => null,
        'start_at' => now(),
        'end_at' => now(),
        'duration_type' => $faker->randomElement(Leave::$duration_types),
        'tenant_id' => 1,
    ];
});