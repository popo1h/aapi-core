<?php

namespace Popo1h\AapiCore\ApiServer;

use Pimple\Container;
use Popo1h\AapiCore\Core\BaseApi;
use Popo1h\AapiCore\Core\Exceptions\ApiDoException\ApiNotFoundException;
use Popo1h\AapiCore\Core\Net;
use Popo1h\AapiCore\Core\Request;
use Popo1h\Support\Objects\StringPack;

class ApiServer
{
    const API_CONTANIER_KEY_PREFIX_BASE_API_INSTANCE = 'ori_base_api_';
    const API_CONTANIER_KEY_PREFIX_API_INTRO = 'intro_';
    const API_CONTANIER_KEY_PREFIX_API_VERSIONS = 'versions_';
    const API_CONTANIER_KEY_PREFIX_DO_API = 'do_';

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
        $this->apiContainer['apis'] = [];
        $this->net = $net;
    }

    /**
     * @param string $apiClass class name of object extends BaseApi
     */
    public function registerApiByBaseApi($apiClass)
    {
        $reflectionClass = new \ReflectionClass($apiClass);
        if ($reflectionClass->isSubclassOf(BaseApi::class) != true) {
            return;
        }

        $apiName = forward_static_call_array([$apiClass, 'getName'], []);
        if (!in_array($apiName, $this->apiContainer['apis'])) {
            $apis = $this->apiContainer['apis'];
            $apis[] = $apiName;
            $this->apiContainer['apis'] = $apis;
        }
        $this->apiContainer[self::API_CONTANIER_KEY_PREFIX_BASE_API_INSTANCE . $apiName] = function () use ($reflectionClass) {
            return $reflectionClass->newInstance();
        };
        $this->apiContainer[self::API_CONTANIER_KEY_PREFIX_API_INTRO . $apiName] = function ($pimple) use ($apiName) {
            return forward_static_call_array([$pimple[self::API_CONTANIER_KEY_PREFIX_BASE_API_INSTANCE . $apiName], 'getName'], []);
        };
        $this->apiContainer[self::API_CONTANIER_KEY_PREFIX_API_VERSIONS . $apiName] = function ($pimple) use ($apiName) {
            return forward_static_call_array([$pimple[self::API_CONTANIER_KEY_PREFIX_BASE_API_INSTANCE . $apiName], 'getVersions'], []);
        };
        $this->apiContainer[self::API_CONTANIER_KEY_PREFIX_DO_API . $apiName] = function ($pimple) use ($apiName) {
            return function ($param, $version) use ($pimple, $apiName) {
                return call_user_func_array([$pimple[self::API_CONTANIER_KEY_PREFIX_BASE_API_INSTANCE . $apiName], 'doApi'], [$param, $version]);
            };
        };
    }

    public function registerInterceptor()
    {
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

        $funcDoApi = $this->apiContainer[self::API_CONTANIER_KEY_PREFIX_DO_API . $apiName];

        if (!is_callable($funcDoApi)) {
            throw (new ApiNotFoundException());
        }

        $response = $funcDoApi($request->getParam(), $request->getVersion());

        return $this->net->respond(StringPack::pack($response));
    }
}
