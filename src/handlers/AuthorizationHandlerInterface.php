<?php

namespace meteocontrol\client\vcomapi\handlers;

use GuzzleHttp\Client;
use meteocontrol\client\vcomapi\UnauthorizedException;

interface AuthorizationHandlerInterface {
    /**
     * @param Client $client
     * @throws UnauthorizedException
     */
    public function handleUnauthorziedException(Client $client);
    /**
     * @param Client $client
     * @param array $options
     * @return array
     */
    public function appendAuthorizationHeader(Client $client, array $options);
}
