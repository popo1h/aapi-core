<?php

namespace Popo1h\AapiCore\Core;

use Popo1h\Support\Interfaces\StringPackInterface;
use Popo1h\Support\Traits\StringPack\JsonTrait;

class Request implements StringPackInterface
{
    use JsonTrait;

    /**
     * @var string
     */
    private $apiName;

    /**
     * @var string|null
     */
    private $version;

    /**
     * @var RequestParam
     */
    private $param;

    /**
     * Request constructor.
     * @param string $apiName
     * @param RequestParam $param
     * @param string|null $version
     */
    public function __construct($apiName, RequestParam $param, $version = null)
    {
        $this->apiName = $apiName;
        $this->version = $version;
        $this->param = $param;
    }

    protected static function getPackPropertyNames()
    {
        return [
            'apiName',
            'version',
            'param',
        ];
    }

    /**
     * @return string
     */
    public function getApiName()
    {
        return $this->apiName;
    }

    /**
     * @return null|string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @return RequestParam
     */
    public function getParam()
    {
        return $this->param;
    }
}
