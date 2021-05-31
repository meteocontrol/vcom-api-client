<?php

namespace meteocontrol\client\vcomapi\handlers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use meteocontrol\client\vcomapi\Config;
use meteocontrol\client\vcomapi\UnauthorizedException;

class BasicAuthorizationHandler implements AuthorizationHandlerInterface {
    /** @var Config */
    private $config;

    /**
     * @param Config $config
     */
    public function __construct(Config $config) {
        $this->config = $config;
    }

    /**
     * @param ClientException $ex
     * @param Client $client
     * @throws UnauthorizedException
     */
    public function handleUnauthorizedException(ClientException $ex, Client $client) {
        throw new UnauthorizedException(
            $ex->getResponse()->getBody()->getContents(),
            $ex->getResponse()->getStatusCode()
        );
    }

    /**
     * @param Client $client
     * @param array $options
     * @return array
     */
    public function appendAuthorizationHeader(Client $client, array $options) {
        $options['headers']['Authorization'] = $this->getBasicAuthString($this->config);
        return $options;
    }

    /**
     * @param Config $config
     * @return string
     */
    private function getBasicAuthString(Config $config) {
        return 'Basic ' . base64_encode($config->getApiUsername() . ':' . $config->getApiPassword());
    }
}
