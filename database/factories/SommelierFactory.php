<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Sommelier::class, function (Faker $faker) {
    return [
        'id'         => 1,
        'first_name' => $faker->name(),
        'last_name'  => $faker->lastName,
        'available'  => true,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s'),
        'deleted_at' => null
    ];
});
