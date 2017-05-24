<?php

namespace Popo1h\AapiCore\ApiServer;

use Popo1h\AapiCore\Core\Exceptions\ApiServerException\ServerNetNotDefineException;
use Popo1h\AapiCore\Core\Exceptions\ApiServerException\ServerNetNotFoundException;
use Popo1h\AapiCore\Core\Net;

class ApiServerFactory
{
    /**
     * @param array $config [ 'net' => 'http', 'apis' => [ 'api class name', ] ]
     * @return ApiServer
     * @throws ServerNetNotDefineException
     */
    public static function createServer(array $config)
    {
        if (!isset($config['net'])) {
            throw (new ServerNetNotDefineException());
        }

        $net = self::getNetByType($config['net']);
        $apiServer = new ApiServer($net);

        if (isset($config['apis'])) {
            foreach ($config['apis'] as $api) {
                $apiServer->registerApiByBaseApi($api);
            }
        }

        return $apiServer;
    }

    /**
     * @param string $netType
     * @return Net\Http
     * @throws ServerNetNotFoundException
     */
    private static function getNetByType($netType)
    {
        switch ($netType) {
            case 'http':
                return (new Net\Http());
        }

        throw (new ServerNetNotFoundException());
    }
}
