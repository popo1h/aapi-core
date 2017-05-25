<?php

namespace Popo1h\AapiCore\ApiServer\DefaultApi;

use Popo1h\AapiCore\ApiServer\ApiServer;
use Popo1h\AapiCore\Core\BaseApi;
use Popo1h\AapiCore\Core\Response;

class ApiListApi extends BaseApi
{
    protected static $versionMap = [
        '1.0.0' => 'getApiList',
    ];

    /**
     * @var ApiServer
     */
    private $apiServer;

    public static function getName()
    {
        return '__api_list';
    }

    public function setApiServer(ApiServer $apiServer)
    {
        $this->apiServer = $apiServer;
    }

    /**
     * @intro 获取api列表
     * @return Response
     */
    public function getApiList()
    {
        $apiContainer = $this->apiServer->getApiContainer();
        $apiNames = $apiContainer[ApiServer::API_CONTAINER_KEY_APIS];

        $list = [];
        foreach ($apiNames as $apiName) {
            $versions = $apiContainer[ApiServer::API_CONTAINER_KEY_PREFIX_API_VERSIONS . $apiName];
            $introFunc = $apiContainer[ApiServer::API_CONTAINER_KEY_PREFIX_API_INTRO . $apiName];
            $versionList = [];
            foreach ($versions as $version) {
                $versionList[] = [
                    'version' => $version,
                    'intro' => (call_user_func_array($introFunc, [$version]) ?: ''),
                ];
            }

            $list[] = [
                'name' => $apiName,
                'versions' => $versionList,
            ];
        }

        return (new Response(Response::CODE_SUCCESS, 'success', [
            'list' => $list,
        ]));
    }
}
