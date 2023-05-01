<?php

use App\Models\Tenant\Employee\Department;
use App\Repositories\Core\Status\StatusRepository;
use Faker\Generator as Faker;

$factory->define(Department::class, function (Faker $faker) {
    return [
        'name' => $faker->company,
        'status_id' => resolve(StatusRepository::class)->departmentActive(),
        'tenant_id' => 1
    ];
});
