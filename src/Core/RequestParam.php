<?php

namespace Popo1h\AapiCore\Core;

use Popo1h\Support\Interfaces\StringPackInterface;
use Popo1h\Support\Traits\StringPack\JsonTrait;

class RequestParam implements StringPackInterface
{
    use JsonTrait;

    /**
     * @var array
     */
    private $paramData;

    /**
     * @param array $paramData
     */
    public function __construct($paramData)
    {
        $this->paramData = $paramData;
    }

    protected static function getPackPropertyNames()
    {
        return [
            'paramData',
        ];
    }

    /**
     * @return array
     */
    public function getParamData()
    {
        return $this->paramData;
    }

    /**
     * @param string $name
     * @param mixed $default
     * @param callable|null $dealFunction
     * @return mixed
     */
    public function getDataByName($name, $default = null, $dealFunction = null)
    {
        if (!isset($this->paramData[$name])) {
            return $default;
        }

        $getData = $this->paramData[$name];
        if (is_callable($dealFunction)) {
            $getData = $dealFunction($getData);
        }

        return $getData;
    }
}
