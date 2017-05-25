<?php

namespace Popo1h\AapiCore\ApiServer;

use Pimple\Container;
use Popo1h\AapiCore\ApiServer\DefaultApi\ApiListApi;
use Popo1h\AapiCore\Core\BaseApi;
use Popo1h\AapiCore\Core\Exceptions\ApiDoException\ApiNotFoundException;
use Popo1h\AapiCore\Core\Net;
use Popo1h\AapiCore\Core\Request;
use Popo1h\Support\Objects\StringPack;

class ApiServer
{
    const API_CONTAINER_KEY_APIS = 'apis';
    const API_CONTAINER_KEY_PREFIX_BASE_API_INSTANCE = 'ori_base_api_';
    const API_CONTAINER_KEY_PREFIX_API_INTRO = 'intro_';
    const API_CONTAINER_KEY_PREFIX_API_VERSIONS = 'versions_';
    const API_CONTAINER_KEY_PREFIX_DO_API = 'do_';

    /**
     * @var Container
     */
    protected $apiContainer;

    /**
     * @var Net
     */
    protected $net;

    /**
     * @param Net $net
     */
    public function __construct(Net $net)
    {
        $this->apiContainer = new Container();
        $this->apiContainer[self::API_CONTAINER_KEY_APIS] = [];
        $this->net = $net;

        $this->registerDefaultApi();
    }

    protected function registerDefaultApi()
    {
        //register ApiListApi
        $apiListApi = new ApiListApi();
        $apiListApi->setApiServer($this);
        $this->registerApiByBaseApi($apiListApi);
    }

    /**
     * @param string|BaseApi $apiClass
     */
    public function registerApiByBaseApi($apiClass)
    {
        $reflectionClass = new \ReflectionClass($apiClass);
        if ($reflectionClass->isSubclassOf(BaseApi::class) != true) {
            return;
        }

        if (is_string($apiClass)) {
            $classType = 0;
        } else {
            $classType = 1;
        }

        $apiName = forward_static_call_array([$apiClass, 'getName'], []);
        if (!in_array($apiName, $this->apiContainer[self::API_CONTAINER_KEY_APIS])) {
            $apis = $this->apiContainer[self::API_CONTAINER_KEY_APIS];
            $apis[] = $apiName;
            $this->apiContainer[self::API_CONTAINER_KEY_APIS] = $apis;
        }
        if ($classType == 0) {
            $this->apiContainer[self::API_CONTAINER_KEY_PREFIX_BASE_API_INSTANCE . $apiName] = function () use ($reflectionClass) {
                return $reflectionClass->newInstance();
            };
        } else {
            $this->apiContainer[self::API_CONTAINER_KEY_PREFIX_BASE_API_INSTANCE . $apiName] = $apiClass;
        }
        $this->apiContainer[self::API_CONTAINER_KEY_PREFIX_API_INTRO . $apiName] = function ($pimple) use ($apiName) {
            return function ($version) use ($pimple, $apiName) {
                return forward_static_call_array([$pimple[self::API_CONTAINER_KEY_PREFIX_BASE_API_INSTANCE . $apiName], 'getIntro'], [$version]);
            };
        };
        $this->apiContainer[self::API_CONTAINER_KEY_PREFIX_API_VERSIONS . $apiName] = function ($pimple) use ($apiName) {
            return forward_static_call_array([$pimple[self::API_CONTAINER_KEY_PREFIX_BASE_API_INSTANCE . $apiName], 'getVersions'], []);
        };
        $this->apiContainer[self::API_CONTAINER_KEY_PREFIX_DO_API . $apiName] = function ($pimple) use ($apiName) {
            return function ($param, $version) use ($pimple, $apiName) {
                return call_user_func_array([$pimple[self::API_CONTAINER_KEY_PREFIX_BASE_API_INSTANCE . $apiName], 'doApi'], [$param, $version]);
            };
        };
    }

    /**
     * @return Container
     */
    public function getApiContainer()
    {
        return $this->apiContainer;
    }

    /**
     * @return string
     * @throws ApiNotFoundException
     */
    public function listen()
    {
        $requestStr = $this->net->receive();
        /**
         * @var Request $request
         */
        $request = StringPack::unpack($requestStr);
        $apiName = $request->getApiName();

        $funcDoApi = $this->apiContainer[self::API_CONTAINER_KEY_PREFIX_DO_API . $apiName];

        if (!is_callable($funcDoApi)) {
            throw (new ApiNotFoundException());
        }

        $response = $funcDoApi($request->getParam(), $request->getVersion());

        return $this->net->respond(StringPack::pack($response));
    }
}
