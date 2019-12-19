<?php

$factory->define(App\MedicalRecord::class, function (Faker\Generator $faker) {
    return [
        'medical_record_number' => $faker->randomNumber(5),
    ];
});
