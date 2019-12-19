<?php

$factory->define(App\User::class, function (Faker\Generator $faker) {
    return [
        'firstname'         => $faker->firstName,
        'lastname'          => $faker->lastName,
        'phone_number'      => $faker->phoneNumber,
        'username'          => $faker->userName,
        'password'          => app('hash')->make('secret'),
        // 'profile_photo'     => $faker->image,
    ];
});

$factory->defineAs('App\User', 'admin', function ($faker) use ($factory) {
    $user = $factory->raw('App\User');

    return array_merge($user, [
        'role_id' => 1
    ]);
});

$factory->defineAs('App\User', 'doctor', function ($faker) use ($factory) {
    $user = $factory->raw('App\User');

    return array_merge($user, [
        'role_id' => 2
    ]);
});

$factory->defineAs('App\User', 'secretary', function ($faker) use ($factory) {
    $user = $factory->raw('App\User');

    return array_merge($user, [
        'role_id' => 3
    ]);
});

$factory->defineAs('App\User', 'patient', function ($faker) use ($factory) {
    $user = $factory->raw('App\User');

    return array_merge($user, [
        'role_id' => 4
    ]);
});