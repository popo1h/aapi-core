<?php

namespace Popo1h\AapiCore\Core;

use Popo1h\Support\Interfaces\StringPackInterface;
use Popo1h\Support\Traits\StringPack\JsonTrait;

class Response implements StringPackInterface
{
    use JsonTrait;

    const CODE_SUCCESS = 1;
    const CODE_ERROR = 0;
    const CODE_REQUEST_ERROR = -1;

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
     * @var string|null
     */
    private $originResponseContent;

    /**
     * @param int $code
     * @param string $msg
     * @param array|null $data
     */
    public function __construct($code = self::CODE_SUCCESS, $msg = '', $data = null)
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
            'originResponseContent',
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

    /**
     * @return null|string
     */
    public function getOriginResponseContent()
    {
        return $this->originResponseContent;
    }

    /**
     * @param null|string $originResponseContent
     */
    public function setOriginResponseContent($originResponseContent)
    {
        $this->originResponseContent = $originResponseContent;
    }
}
