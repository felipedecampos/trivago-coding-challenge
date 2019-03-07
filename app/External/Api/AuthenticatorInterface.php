<?php

namespace App\External\Api;


/**
 * Interface AuthenticatorInterface
 *
 * An interface to add additional authentication parameters to the
 * request
 *
 * @package App\External\Api
 * @author Felipe de Campos <felipe.campos.programador@gmail.com>
 */
interface AuthenticatorInterface
{

    /**
     * Modifies guzzle options to meet authentication requirements for specific API
     *
     * @param array $options
     * @param mixed $data
     * @return null
     */
    public function modifyOptions(array &$options, $data = null);

}
