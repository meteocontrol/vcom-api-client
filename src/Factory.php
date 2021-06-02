<?php

namespace meteocontrol\client\vcomapi;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use meteocontrol\client\vcomapi\handlers\AuthorizationHandlerInterface;
use meteocontrol\client\vcomapi\handlers\BasicAuthorizationHandler;
use meteocontrol\client\vcomapi\handlers\OAuthAuthorizationHandler;

class Factory {

    private const API_VERSION = 'v2';

    /**
     * @param Config|null $config
     * @param ClientInterface|null $client
     * @return ApiClient
     */
    public function getApiClient(Config $config = null, ClientInterface $client = null): ApiClient {
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
    public static function getDefaultHeaders(Config $config): array {
        return [
            'X-API-KEY' => $config->getApiKey(),
            'Accept' => '*/*'
        ];
    }

    /**
     * @param Config $config
     * @return Client
     */
    public static function getHttpClient(Config $config): Client {
        $baseUri = sprintf("%s/%s/", $config->getApiUrl(), self::API_VERSION);

        return new Client(
            [
                'base_uri' => $baseUri,
                'headers' => self::getDefaultHeaders($config),
                'debug' => false
            ]
        );
    }

    /**
     * @param Config $config
     * @return AuthorizationHandlerInterface
     */
    public static function getAuthorizationHandler(Config $config): AuthorizationHandlerInterface {
        return $config->getApiAuthorizationMode() === 'basic' ?
            new BasicAuthorizationHandler($config) :
            new OAuthAuthorizationHandler($config);
    }
}
