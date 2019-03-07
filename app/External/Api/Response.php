<?php

namespace App\External\Api;

use Psr\Http\Message\ResponseInterface as PsrResponseInterface;

/**
 * Class Response
 * @package App\External\Api
 * @author Felipe de Campos <felipe.campos.programador@gmail.com>
 */
class Response implements ResponseInterface
{

    /**
     * @var \Psr\Http\Message\ResponseInterface
     */
    protected $response;

    /**
     * @var mixed
     */
    protected $data;

    public function __construct(PsrResponseInterface $response)
    {
        $this->response = $response;
    }

    /**
     * @return int The response's status code
     */
    public function getStatusCode()
    {
        return $this->response->getStatusCode();
    }

    /**
     * @return bool Whether the request resulted in an http error response
     */
    public function isError()
    {
        return floor($this->response->getStatusCode() / 100) != 2;
    }

    /**
     * @return mixed The data returned from the API
     */
    public function getData()
    {
        if (empty($this->data)) {
            $this->data = (string) $this->response->getBody();
        }

        return $this->data;
    }

}
