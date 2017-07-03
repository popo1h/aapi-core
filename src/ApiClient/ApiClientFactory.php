<?php

namespace Popo1h\AapiCore\ApiClient;

use Popo1h\AapiCore\Core\Exceptions\AapiCoreException\ApiClientException\ClientNetNotDefineException;
use Popo1h\AapiCore\Core\Exceptions\AapiCoreException\ApiClientException\ClientNetNotFoundException;
use Popo1h\AapiCore\Core\Exceptions\AapiCoreException\ApiClientException\ClientServerUrlNotDefineException;
use Popo1h\AapiCore\Core\Net;

class ApiClientFactory
{
    /**
     * @param array $config [ 'net' => 'http', 'server' => '' ]
     * @return ApiClient
     * @throws ClientNetNotDefineException
     * @throws ClientServerUrlNotDefineException
     */
    public static function createClient(array $config)
    {
        if (!isset($config['net'])) {
            throw (new ClientNetNotDefineException());
        }

        if(!isset($config['server'])){
            throw (new ClientServerUrlNotDefineException());
        }
        $serverUrl = $config['server'];

        $net = self::getNetByType($config['net']);
        $apiClient = new ApiClient($serverUrl, $net);

        return $apiClient;
    }

    /**
     * @param string $netType
     * @return Net\Http
     * @throws ClientNetNotFoundException
     */
    private static function getNetByType($netType)
    {
        switch ($netType) {
            case 'http':
                return (new Net\Http());
        }

        throw (new ClientNetNotFoundException());
    }
}
