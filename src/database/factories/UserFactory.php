<?php

use App\Models\Core\Auth\User;
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

$factory->define(User::class, function (Generator $faker) {
    return [
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'email' => $faker->unique()->safeEmail,
        'password' => '123456',
        'status_id' => resolve(StatusRepository::class)->userActive(),
        'is_in_employee' => 1
    ];
});
