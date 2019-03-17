<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Order::class, function (Faker $faker) {
    return [
        'id'           => $faker->unique()->randomNumber(3),
        'status'       => $faker->randomElement(['open', 'preparing', 'closed']),
        'user_id'      => $faker->randomNumber(),
        'waiter_id'    => $faker->numberBetween(1, 2),
        'sommelier_id' => 1,
        'created_at'   => $faker->date('Y-m-d H:i:s'),
        'updated_at'   => $faker->date('Y-m-d H:i:s'),
        'deleted_at'   => null
    ];
});
