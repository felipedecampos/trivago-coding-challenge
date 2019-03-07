<?php

use App\External\Api\JsonResponse;

return [

    'wine-spectator' => [
        'base_uri'       => 'https://www.winespectator.com/rss',
        'rss'            => 'rss?t=dwp',
        'exceptions'     => false, // Don't throw exceptions, instead use: $response->isError()
        'api_version'    => null, // Don't add the /vXX to the URL
        'data_type'      => 'json', // Will send everything as json
        'response_class' => JsonResponse::class, //Defined response class
        'log_file'       => __DIR__ . '/../storage/logs/winespectator.log'
    ]

];
