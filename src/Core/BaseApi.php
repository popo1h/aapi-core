<?php

namespace Popo1h\AapiCore\Core;

use Popo1h\AapiCore\Core\Exceptions\ApiDoException\ApiVersionNotFoundException;

abstract class BaseApi
{
    /**
     * [ 'version' => 'method_name' ]
     * sample: [ '1.0.0' => 'testApi' ]
     * @var array
     */
    protected static $versionMap = [];

    public static function getName()
    {
        return static::class;
    }

    public static function getIntro($version = null)
    {
        static::getMethodNameByVersion($version);

        return '';
    }

    public static function getVersions()
    {
        return array_keys(static::$versionMap);
    }

    public static function getDefaultVersion()
    {
        static $defaultVersion;

        if (!isset($defaultVersion)) {
            foreach (static::$versionMap as $version => $methodName) {
                if (version_compare($version, $defaultVersion, '>')) {
                    $defaultVersion = $version;
                }
            }
        }

        return $defaultVersion;
    }

    protected static function getMethodNameByVersion($version = null)
    {
        if (!isset($version)) {
            $version = static::getDefaultVersion();
        }

        if (!isset(static::$versionMap[$version])) {
            throw (new ApiVersionNotFoundException());
        }

        return static::$versionMap[$version];
    }

    /**
     * @param RequestParam $param
     * @param string|null $version
     * @return Response
     * @throws ApiVersionNotFoundException
     */
    public function doApi(RequestParam $param, $version = null)
    {
        $methodName = static::getMethodNameByVersion($version);

        if (!is_callable([$this, $methodName])) {
            throw (new ApiVersionNotFoundException());
        }

        return call_user_func_array([$this, $methodName], [$param]);
    }
}
