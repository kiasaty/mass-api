<?php

$factory->define(App\WorkSchedule::class, function (Faker\Generator $faker) {

    $startTime = $faker->time('H:i');

    $endTime_timestamp = strtotime($startTime) + (60 * 60 * 2);
    $endTime = date('H:i', $endTime_timestamp);

    return [
        'day_of_week'   => getDayNumber($faker->dayOfWeek),
        'start_time'    => $startTime,
        'end_time'      => $endTime,
    ];
});
