<?php

namespace meteocontrol\client\vcomapi\handlers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use meteocontrol\client\vcomapi\Config;
use meteocontrol\client\vcomapi\UnauthorizedException;

class OAuthAuthorizationHandler implements AuthorizationHandlerInterface {

    /** @var string */
    private $accessToken;
    /** @var string */
    private $refreshToken;
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
        $this->doOAuthRefresh($client);
    }

    /**
     * @param Client $client
     * @param array $options
     * @return array
     */
    public function appendAuthorizationHeader(Client $client, array $options) {
        if (empty($this->accessToken)) {
            $this->doOAuthGrant($client);
            $options['headers']['Authorization'] = sprintf('Bearer %s', $this->accessToken);
        } else {
            $options['headers']['Authorization'] = sprintf('Bearer %s', $this->accessToken);
        }
        return $options;
    }

    /**
     * @param Client $client
     * @return string
     * @throws UnauthorizedException
     */
    private function doOAuthGrant(Client $client) {
        try {
            $response = $client->post(
                sprintf('%s/login', $this->config->getApiUrl()),
                [
                    'form_params' => [
                        'grant_type' => 'password',
                        'username' => $this->config->getApiUsername(),
                        'password' => $this->config->getApiPassword(),
                        'client_id' => 'mc-php-client',
                        'client_secret' => 'jtL1FR9xg]NX4Mq<mzre}F-TypZ_k,ib'
                    ]
                ]
            );
            $credentials = json_decode($response->getBody()->getContents(), true);
            $this->accessToken = $credentials['access_token'];
            $this->refreshToken = $credentials['refresh_token'];
        } catch (ClientException $ex) {
            if (!in_array($ex->getResponse()->getStatusCode(), [400, 401, 403])) {
                throw $ex;
            }
            throw new UnauthorizedException($ex->getResponse()->getBody()->getContents());
        }
    }

    /**
     * @param Client $client
     * @throws UnauthorizedException
     */
    private function doOAuthRefresh(Client $client) {
        try {
            $response = $client->post(
                sprintf('%s/login', $this->config->getApiUrl()),
                [
                    'form_params' => [
                        'grant_type' => 'refresh_token',
                        'refresh_token' => $this->refreshToken,
                        'client_id' => 'mc-php-client',
                        'client_secret' => 'jtL1FR9xg]NX4Mq<mzre}F-TypZ_k,ib'
                    ]
                ]
            );
            $credentials = json_decode($response->getBody()->getContents(), true);
            $this->accessToken = $credentials['access_token'];
            $this->refreshToken = $credentials['refresh_token'];
        } catch (ClientException $ex) {
            if (!in_array($ex->getResponse()->getStatusCode(), [400, 401, 403])) {
                throw $ex;
            }
            throw new UnauthorizedException($ex->getResponse()->getBody()->getContents());
        }
    }
}
