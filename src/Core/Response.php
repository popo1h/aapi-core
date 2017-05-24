<?php

namespace Popo1h\AapiCore\Core;

use Popo1h\Support\Interfaces\StringPackInterface;
use Popo1h\Support\Traits\StringPack\JsonTrait;

class Response implements StringPackInterface
{
    use JsonTrait;

    const CODE_SUCCESS = 1;
    const CODE_ERROR = 0;

    /**
     * @var int
     */
    private $code;

    /**
     * @var string
     */
    private $msg;

    /**
     * @var array|null
     */
    private $data;

    /**
     * @param int $code
     * @param string $msg
     * @param array|null $data
     */
    public function __construct($code, $msg, $data)
    {
        $this->code = $code;
        $this->msg = $msg;
        $this->data = $data;
    }

    protected static function getPackPropertyNames()
    {
        return [
            'code',
            'msg',
            'data',
        ];
    }

    /**
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getMsg()
    {
        return $this->msg;
    }

    /**
     * @return array|null
     */
    public function getData()
    {
        return $this->data;
    }
}
