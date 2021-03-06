<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

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

$factory->define(User::class, function (Faker $faker) {
    $type = ["web", "mobile"];
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        "type" => $type[$faker->numberBetween(0, 1)],
        'email_verified_at' => now(),
        'password' => '$2y$10$qiQFOkim.DjVSJcsRmZesezfz0Lb0KqBABae.bqJ7.uyWStzD/FSS', // password
        'remember_token' => Str::random(10),
    ];
});
