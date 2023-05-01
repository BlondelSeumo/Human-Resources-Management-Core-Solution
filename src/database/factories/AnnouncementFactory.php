<?php

use App\Models\Tenant\Employee\Announcement\Announcement;
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

$factory->define(Announcement::class, function (Generator $faker) {
    return [
        'name' => $faker->name,
        'start_date' => today()->subDays(1),
        'end_date' => today()->addDays(2),
        'description' => $faker->sentence,
        'tenant_id' => 1,
    ];
});