<?php

namespace App\External\WineSpectator;

use App\External\Api\AuthenticatorInterface;
use Illuminate\Support\Facades\Log;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

/**
 * Class Authenticator
 * @package Application\External\WineSpectator
 * @author Felipe de Campos <felipe.campos.programador@gmail.com>
 */
class WineSpectatorAuthenticator implements AuthenticatorInterface
{

    /**
     * {@inheritdoc}
     */
    public function modifyOptions(array &$options, $data = null)
    {
        $options['headers']['Content-Type'] = 'application/xml; charset=utf-8';

        if (isset($options['log_file']))
        {
            $logger = new RotatingFileHandler($options['log_file']);
            $logger->setFormatter(
                new LineFormatter(null, null, true, true)
            );

            $options['logger'] = new Logger('client', [$logger]);

            unset($options['log_file']);
        }

        Log::info(sprintf("Request to wine-spectator: 
Date: %s
Response: %s",
            (new \DateTime('now'))->format('Y-m-d - H:i:s'),
            print_r($data, true)
        ), ['single' => [
        'driver' => 'single',
        'tap' => [App\Logging\CustomizeFormatter::class],
        'path' => storage_path('logs/laravel.log'),
        'level' => 'debug',
    ]]);

//        app('monitoring.mec.returns')->info(sprintf("Request to {$options["base_uri"]}/{$options["rss"]}
//date: %s
//Post: %s",
//            (new \DateTime('now'))->format('Y-m-d - H:i:s'),
//            print_r($data, true)
//        ));
    }

}
