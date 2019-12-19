<?php

$factory->define(App\Experiment::class, function (Faker\Generator $faker) {
    return [
        'title'         => $faker->word,
        'description'   => $faker->text,
    ];
});
