<?php

namespace Popo1h\AapiCore\ApiClient;

use Popo1h\Support\Traits\Instances\InstancePoolTrait;

class Manager
{
    use InstancePoolTrait;

    public function addClient(array $config, $name = 'default')
    {
        $client = ApiClientFactory::createClient($config);

        $this->pushInstanceIntoPool($client, $name);
    }

    /**
     * @param string $name
     * @return ApiClient
     */
    public function getClient($name = 'default')
    {
        return $this->getInstanceFromPool($name);
    }
}
