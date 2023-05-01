<?php

use App\Models\Tenant\WorkingShift\WorkingShift;
use App\Repositories\Core\Status\StatusRepository;
use Faker\Generator;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(WorkingShift::class, function (Generator $faker) {
    return [
        'name' => $faker->name,
        'start_date' => today()->subDays(100)->format('Y-m-d'),
        'end_date' => today()->addDays(100),
        'description' => $faker->sentence,
        'tenant_id' => 1,
        'is_default' => 0,
        'type' => 'scheduled'
    ];
});
