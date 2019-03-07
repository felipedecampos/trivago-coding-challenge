<?php

namespace App\External\Api;

use Psr\Http\Message\ResponseInterface as PsrResponseInterface;

/**
 * Interface ResponseInterface
 *
 * An interface to represent an API response, with data returned from the API
 * and other information
 *
 * @package App\External\Api
 * @author Felipe de Campos <felipe.campos.programador@gmail.com>
 */
interface ResponseInterface
{

    public function __construct(PsrResponseInterface $response);

    /**
     * @return int The response's status code
     */
    public function getStatusCode();

    /**
     * @return bool Whether the request resulted in an http error response
     */
    public function isError();

    /**
     * @return mixed The data returned from the API
     */
    public function getData();

}
