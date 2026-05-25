<?php

namespace meteocontrol\client\vcomapi\handlers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use meteocontrol\client\vcomapi\Config;
use meteocontrol\client\vcomapi\UnauthorizedException;

class BasicAuthorizationHandler implements AuthorizationHandlerInterface {

    /** @var Config */
    private $config;

    public function __construct(Config $config) {
        $this->config = $config;
    }

    /**
     * @param ClientException $ex
     * @param Client $client
     * @return void
     * @throws UnauthorizedException
     */
    public function handleUnauthorizedException(ClientException $ex, Client $client): void {
        throw new UnauthorizedException(
            $ex->getResponse()->getBody()->getContents(),
            $ex->getResponse()->getStatusCode()
        );
    }

    public function appendAuthorizationHeader(Client $client, array $options): array {
        $options['headers']['Authorization'] = $this->getBasicAuthString($this->config);
        return $options;
    }

    private function getBasicAuthString(Config $config): string {
        return 'Basic ' . base64_encode($config->getApiUsername() . ':' . $config->getApiPassword());
    }
}
