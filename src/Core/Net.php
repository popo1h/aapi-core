<?php

namespace Popo1h\AapiCore\Core;

abstract class Net
{
    /**
     * @param string $apiUrl
     * @param string $requestStr
     * @param array|null $hostIps
     * @return string responseStr
     */
    abstract public function request($apiUrl, $requestStr, $hostIps = null);

    /**
     * @return string
     */
    abstract public function receive();

    /**
     * @param string $responseStr
     * @return string
     */
    abstract public function respond($responseStr);
}
