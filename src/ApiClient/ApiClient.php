<?php

namespace Popo1h\AapiCore\ApiClient;

use Popo1h\AapiCore\Exceptions\AapiCoreException\ApiDoException\ApiResponseErrorException;
use Popo1h\AapiCore\Core\Net;
use Popo1h\AapiCore\Core\Request;
use Popo1h\AapiCore\Core\RequestParam;
use Popo1h\AapiCore\Core\Response;
use Popo1h\Support\Objects\StringPack;

class ApiClient
{
    /**
     * @var Net
     */
    protected $net;

    /**
     * @var string
     */
    protected $serverUrl;

    /**
     * @param Net $net
     */
    public function __construct($serverUrl, Net $net)
    {
        $this->net = $net;
        $this->serverUrl = $serverUrl;
    }

    /**
     * @param string $apiName
     * @param RequestParam $param
     * @param string|null $version
     * @param array|null $hostIps
     * @return Response
     * @throws ApiResponseErrorException
     */
    public function request($apiName, RequestParam $param, $version = null, $hostIps = null)
    {
        $request = new Request($apiName, $param, $version);

        $apiUrl = $this->serverUrl;

        $responseStr = $this->net->request($apiUrl, StringPack::pack($request), $hostIps);
        try {
            $response = StringPack::unpack($responseStr);
        } catch (\Exception $e) {
            throw (new ApiResponseErrorException($responseStr));
        }

        return $response;
    }
}
