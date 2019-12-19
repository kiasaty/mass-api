<?php

$factory->define(App\Medicine::class, function (Faker\Generator $faker) {
    return [
        'title'         => $faker->word,
        'description'   => $faker->text,
    ];
});
