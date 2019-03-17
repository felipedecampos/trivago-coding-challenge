<?php

use Faker\Generator as Faker;

$factory->define(App\Models\WineOrder::class, function (Faker $faker) {
    return [
        'status'    => $faker->randomElement(['placed', 'delivered', 'unavailable']),
        'order_id'  => $faker->randomNumber(),
        'wine_guid' => $faker->randomElement([
            'https://www.winespectator.com/dailypicks/show/date/2019-03-08/dwpid/15361',
            'https://www.winespectator.com/dailypicks/show/date/2019-03-08/dwpid/15362',
            'https://www.winespectator.com/dailypicks/show/date/2019-03-08/dwpid/15363',
            'https://www.winespectator.com/dailypicks/show/date/2019-03-07/dwpid/15358',
            'https://www.winespectator.com/dailypicks/show/date/2019-03-07/dwpid/15360',
            'https://www.winespectator.com/dailypicks/show/date/2019-03-06/dwpid/15356',
        ])
    ];
});
