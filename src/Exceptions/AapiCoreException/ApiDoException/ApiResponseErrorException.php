<?php

namespace Popo1h\AapiCore\Core\Exceptions\AapiCoreException\ApiDoException;

use Popo1h\AapiCore\Core\Exceptions\AapiCoreException\ApiDoException;

class ApiResponseErrorException extends ApiDoException
{
    /**
     * @var string
     */
    private $responseStr;

    public function __construct($responseStr)
    {
        $this->responseStr = $responseStr;

        parent::__construct('', 0, null);
    }

    /**
     * @return string
     */
    public function getResponseStr()
    {
        return $this->responseStr;
    }
}
