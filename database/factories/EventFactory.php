<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;
use Illuminate\Support\Carbon;

$factory->define(App\Event::class, function (Faker $faker) {
    $date = Carbon::parse($faker->date());
    return [
        'title' => $faker->text,
        'start_date' => $date->toDateString(),
        'end_date' => $date->addDays($faker->numberBetween(1, 4))->toDateString(),
        'type' => $faker->randomElement(['W', 'T', 'C']),
        'grouping' => $faker->randomElement(['R', 'L', 'M', 'N']),
    ];
});
