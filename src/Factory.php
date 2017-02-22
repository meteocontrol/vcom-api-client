<?php

namespace meteocontrol\client\vcomapi;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

class Factory {

    /**
     * @param Config $config
     * @param ClientInterface $client
     * @return ApiClient
     */
    public function getApiClient(Config $config = null, ClientInterface $client = null) {
        $config = $config ?: new Config();
        $config->validate();

        if (!$client) {
            $client = self::getHttpClient($config);
        }

        return new ApiClient(
            $config,
            $client
        );
    }

    /**
     * @param Config $config
     * @return array
     */
    public static function getAuthorizationHeaders(Config $config) {
        return [
            'X-API-KEY' => $config->getApiKey(),
            'Authorization' => self::getBasicAuthString($config),
            'Accept' => '*/*'
        ];
    }

    /**
     * @param Config $config
     * @return Client
     */
    public static function getHttpClient(Config $config) {
        $client = new Client(
            [
                'base_uri' => $config->getApiUrl() . '/',
                'headers' => self::getAuthorizationHeaders($config),
                'debug' => false
            ]
        );
        return $client;
    }

    /**
     * @param Config $config
     * @return string
     */
    private static function getBasicAuthString(Config $config) {
        return 'Basic ' . base64_encode($config->getApiUsername() . ':' . $config->getApiPassword());
    }
}
