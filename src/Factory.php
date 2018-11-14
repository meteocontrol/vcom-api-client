<?php

namespace meteocontrol\client\vcomapi;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use meteocontrol\client\vcomapi\handlers\AuthorizationHandlerInterface;
use meteocontrol\client\vcomapi\handlers\BasicAuthorizationHandler;
use meteocontrol\client\vcomapi\handlers\OAuthAuthorizationHandler;

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
            $client,
            self::getAuthorizationHandler($config)
        );
    }

    /**
     * @param Config $config
     * @return array
     */
    public static function getDefaultHeaders(Config $config) {
        return [
            'X-API-KEY' => $config->getApiKey(),
            'Accept' => '*/*'
        ];
    }

    /**
     * @param Config $config
     * @return Client
     */
    public static function getHttpClient(Config $config) {
        $baseUri = $config->getApiUrl() . '/v2';
        $client = new Client(
            [
                'base_uri' => $baseUri,
                'headers' => self::getDefaultHeaders($config),
                'debug' => false
            ]
        );
        return $client;
    }

    /**
     * @param Config $config
     * @return AuthorizationHandlerInterface
     */
    public static function getAuthorizationHandler(Config $config) {
        return $config->getApiAuthorizationMode() === 'oauth' ?
            new OAuthAuthorizationHandler($config) :
            new BasicAuthorizationHandler($config);
    }
}
