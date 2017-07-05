<?php

namespace meteocontrol\client\vcomapi\handlers;

use GuzzleHttp\Client;
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
     * @param Client $client
     * @throws UnauthorizedException
     */
    public function handleUnauthorziedException(Client $client) {
        throw new UnauthorizedException('Unauthorized. Please check your username and password!');
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
