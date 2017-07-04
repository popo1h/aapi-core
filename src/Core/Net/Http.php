<?php

namespace Popo1h\AapiCore\Core\Net;

use Popo1h\AapiCore\Core\Net;

class Http extends Net
{
    public function request($apiUrl, $requestStr, $hostIps = null)
    {
        $httpHeader = [];
        if (isset($hostIps)) {
            if (is_array($hostIps)) {
                $hostIp = $hostIps[mt_rand(0, count($hostIps) - 1)];
            } elseif (is_string($hostIps)) {
                $hostIp = $hostIps;
            }

            if (isset($hostIp)) {
                $startOffset = strpos($apiUrl, '//') + 2;
                $hostLength = strpos($apiUrl, '/', $startOffset) - $startOffset;

                $host = substr($apiUrl, $startOffset, $hostLength);
                $httpHeader[] = 'Host: ' . $host;
                $apiUrl = substr($apiUrl, 0, $startOffset) . $hostIp . substr($apiUrl, $startOffset + $hostLength);
            }
        }
        $postData = [
            'data' => $requestStr,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeader);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        $responseStr = curl_exec($ch);
        curl_close($ch);

        return $responseStr;
    }

    public function receive()
    {
        $data_res = $_POST['data'];

        return $data_res;
    }

    public function respond($responseStr)
    {
        return $responseStr;
    }
}
