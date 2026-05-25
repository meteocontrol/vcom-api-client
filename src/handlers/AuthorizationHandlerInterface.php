<?php

namespace meteocontrol\client\vcomapi\handlers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use meteocontrol\client\vcomapi\UnauthorizedException;

interface AuthorizationHandlerInterface {
    /**
     * @param ClientException $ex
     * @param Client $client
     * @return void
     * @throws UnauthorizedException
     */
    public function handleUnauthorizedException(ClientException $ex, Client $client): void;

    public function appendAuthorizationHeader(Client $client, array $options): array;
}
