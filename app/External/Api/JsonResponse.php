<?php

namespace App\External\Api;


/**
 * Class JsonResponse
 * @package App\External\Api
 * @author Felipe de Campos <felipe.campos.programador@gmail.com>
 */
class JsonResponse extends Response
{

    /**
     * @var mixed
     */
    protected $json;

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getJson()
    {
        if (! $this->json)
        {
            $this->json = json_decode($this->getData(), true);

            if (! $this->json && json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Error while decoding json content: ' . $this->getData());
            }
        }

        return $this->json;
    }

}
