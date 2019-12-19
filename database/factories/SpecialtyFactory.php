<?php

$factory->define(App\Specialty::class, function (Faker\Generator $faker) {
    return [
        'title'         => $faker->word,
        'description'   => $faker->text,
    ];
});
