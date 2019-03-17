<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Waiter::class, function (Faker $faker) {
    return [
        'id'         => $faker->numberBetween(1, 2),
        'first_name' => $faker->randomElement(['Richard', 'Paul']),
        'last_name'  => $faker->randomElement(['Goodman', 'Priestly']),
        'available'  => true,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s'),
        'deleted_at' => null
    ];
});
