<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(Thoughts\User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'username' => $faker->userName . str_random(3),
        'email' => $faker->unique()->safeEmail,
        'remember_token' => str_random(10),
        'avatar' => asset('img/placeholder.png'),
    ];
});
