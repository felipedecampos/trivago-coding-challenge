<?php

namespace App\External\Api;

use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface as PsrRequestInterface;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class Client
 *
 * A simple class to make API requests
 *
 * @package App\External\Api
 * @author Felipe de Campos <felipe.campos.programador@gmail.com>
 */
class Client
{

    /** @var array */
    protected $options;

    /** @var \GuzzleHttp\Client */
    protected $guzzleClient;

    /** @var \GuzzleHttp\HandlerStack */
    protected $guzzleHandlerStack;

    /** @var array */
    protected $loggerInfo;

    /**
     * Client constructor.
     *
     * @param array $options
     */
    public function __construct(array $options)
    {
        $this->configureDefaultOptions($options);
    }

    /**
     * Makes a request to an API
     *
     * @param string $uri
     * @param string $method
     * @param null   $data If specified, data will be sent as json to the API
     * @param array  $options
     *
     * @throws \Exception
     *
     * @return ResponseInterface
     */
    public function request($uri, $method = 'GET', $data = null, array $options = [])
    {
        $options_merged = $this->prepareDefaultOptions($options);

        if (isset($options_merged['authenticator'])) {
            $options_merged['authenticator']->modifyOptions($options);
        }

        //Data will always be sent as json in this client
        if (! empty($data))
        {
            switch ($options_merged['data_type'])
            {
                case 'json':
                case 'form_params':
                    $options[$options_merged['data_type']] = $data;
                    break;
                case 'raw':
                    $options['body'] = $data;
                    if (empty($options['headers'])) {
                        $options['headers'] = [];
                    }
                    if (! isset($options['headers']['Content-Type'])) {
                        $options['headers']['Content-Type'] = 'text/plain; charset=' . $options_merged['charset'];
                    }
                    break;
                default:
                    throw new \InvalidArgumentException('Unknown data type: ' . $options_merged['data_type']);
                    break;
            }
        }

        $options['base_uri'] = $this->getBaseUri($options_merged);

        if (isset($options_merged['logger']))
        {
            $options['handler'] = $this->getGuzzleHandlerStack(
                $options_merged['logger'], $options_merged['log_format']
            );
        }

        $response = $this->getClient()->request($method, $uri, $options);

        $response_class = null;
        if (isset($options_merged['response_class'])) {
            $response_class = $options_merged['response_class'];
        } else {
            $response_class = Response::class;
        }

        return new $response_class($response);
    }


    /**
     * Makes a GET request to the api
     *
     * @param $uri
     * @param array $query
     *
     * @return ResponseInterface|array
     */
    public function get($uri, $query = [])
    {
        $options = [];
        if (! empty($query)) {
            $options['query'] = $query;
        }

        return $this->request($uri, 'GET', null, $options);
    }

    /**
     * Makes a POST request to the API
     *
     * @param $uri
     * @param array $data
     * @param array $options
     *
     * @return ResponseInterface|array
     */
    public function post($uri, $data = null, array $options = [])
    {
        return $this->request($uri, 'POST', $data, $options);
    }

    /**
     * Makes a PUT request to the API
     *
     * @param $uri
     * @param array $data
     * @param array $options
     *
     * @return ResponseInterface|array
     */
    public function put($uri, $data = null, array $options = [])
    {
        return $this->request($uri, 'PUT', $data, $options);
    }

    /**
     * Makes a DELETE request to the API
     *
     * @param $uri
     * @param array $options
     *
     * @return ResponseInterface|array
     */
    public function delete($uri, array $options = [])
    {
        return $this->request($uri, 'DELETE', null, $options);
    }

    /**
     * Configures the default options for a client.
     *
     * @param array $options
     */
    protected function configureDefaultOptions(array $options)
    {
        if (! isset($options['base_uri'])) {
            throw new \InvalidArgumentException("Option base_uri is required");
        }

        $defaults = [
            //Guzzle options, as specified by guzzle documentation
            'timeout'         => 10,
            'allow_redirects' => true,
            'http_errors'     => true,
            'verify'          => false,

            //Custom options
            'logger'          => null, //Must be an instance of \Monolog\Logger
            'log_format'      => //The log format
                "--------------------------{ts}--------------------------\n" .
                "REQUEST: \n{request}\n\n" . //Request Message Format
                "RESPONSE: \n{response}\n\n" //Response Message Format
            ,
            //A class instance of AuthenticatorInterface that adds additional authentication parameters
            'authenticator'   => null,
            //A class instance of AuthenticatorInterface that adds additional authentication parameters
            'response_class'  => null,
            //This will be appended to the base_uri: {base_uri}/v{api_version}/
            'api_version'     => null,
            //This can be json, form_params or raw
            'data_type'       => 'json',
            //The content type charset, unnecessary to specify for JSON format
            'charset'         => 'UTF-8'
        ];

        $this->options = $options + $defaults;
    }

    /**
     * @return \GuzzleHttp\Client
     */
    protected function getClient()
    {
        if (! $this->guzzleClient) {
            $this->guzzleClient = new \GuzzleHttp\Client($this->options);
        }

        return $this->guzzleClient;
    }

    /**
     * Appends the version to the base_uri
     *
     * @param array $options
     * @return string
     */
    protected function getBaseUri($options = null)
    {
        if (is_null($options)) {
            $options = $this->options;
        }

        //Add the version to the base uri, so it will become something like
        //http://baseuri.com/v1/
        $base_uri = rtrim($options['base_uri'], '/');
        if (! empty($options['api_version'])) {
            $base_uri .= "/v{$options['api_version']}";
        }
        $base_uri .= '/';

        return $base_uri;
    }

    /**
     * Merges options with defaults and validates
     *
     * @param $options
     * @return array
     */
    protected function prepareDefaultOptions($options)
    {
        $options += $this->options;

        if (isset($options['authenticator']) && ! $options['authenticator'] instanceof AuthenticatorInterface) {
            throw new \InvalidArgumentException('authenticator option must implement: ' . AuthenticatorInterface::class);
        }

        if (isset($options['logger']) && ! $options['logger'] instanceof LoggerInterface) {
            throw new \InvalidArgumentException('logger option must implement: ' . LoggerInterface::class);
        }

        if (isset($options['response_class']))
        {
            //Might throw ReflectionException if class doesn't exists
            $rc = new \ReflectionClass($options['response_class']);
            if (! $rc->implementsInterface(ResponseInterface::class)) {
                throw new \InvalidArgumentException('response_class option must implement: ' . ResponseInterface::class);
            }
        }

        return $options;
    }

    private function getGuzzleHandlerStack($logger, $messageFormat)
    {
        if (! $this->guzzleHandlerStack ||
            $this->loggerInfo['logger'] !== $logger ||
            $this->loggerInfo['message_format'] !== $messageFormat
        ) {
            $this->guzzleHandlerStack = HandlerStack::create();

            $this->guzzleHandlerStack->push(
                Middleware::log(
                    $logger,
                    new MessageFormatter($messageFormat)
                )
            );

            $this->loggerInfo = [
                'logger' => $logger,
                'message_format' => $messageFormat
            ];
        }

        return $this->guzzleHandlerStack;
    }

}
