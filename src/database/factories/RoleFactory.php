<?php


use App\Models\Core\Auth\Role;
use App\Models\Core\Auth\Type;
use Faker\Generator as Faker;

$factory->define(Role::class, function (Faker $faker) {
    return [
        'name' => $faker->word,
        'type_id' => Type::findByAlias('tenant')->id,
        'is_default' => $faker->randomElement([0, 1])
    ];
});
