<?php

namespace meteocontrol\client\vcomapi\handlers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use meteocontrol\client\vcomapi\Config;
use meteocontrol\client\vcomapi\Factory;
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
     * @param ClientException $ex
     * @param Client $client
     * @return void
     * @throws UnauthorizedException
     */
    public function handleUnauthorizedException(ClientException $ex, Client $client): void {
        try {
            $this->doOAuthRefresh($client);
        } catch (UnauthorizedException $ex) {
            if ($ex->getMessage() === 'Invalid API key') {
                throw $ex;
            }
            $this->doOAuthGrant($client);
        }
    }

    /**
     * @param Client $client
     * @param array $options
     * @return array
     */
    public function appendAuthorizationHeader(Client $client, array $options): array {
        if (empty($this->accessToken) && !$this->parseCredentials()) {
            $this->doOAuthGrant($client);
        }
        $options['headers']['Authorization'] = sprintf('Bearer %s', $this->accessToken);
        return $options;
    }

    /**
     * @param Client $client
     * @return void
     * @throws UnauthorizedException
     */
    private function doOAuthGrant(Client $client): void {
        $loginUri = sprintf('%s/%s/login', $this->config->getApiUrl(), Factory::API_VERSION);
        try {
            $response = $client->post(
                $loginUri,
                [
                    'form_params' => [
                        'grant_type' => 'password',
                        'username' => $this->config->getApiUsername(),
                        'password' => $this->config->getApiPassword()
                    ]
                ]
            );
            $credentials = json_decode($response->getBody()->getContents(), true);
            $this->accessToken = $credentials['access_token'];
            $this->refreshToken = $credentials['refresh_token'];
            $this->storeCredentials($credentials);
        } catch (ClientException $ex) {
            if (!in_array($ex->getResponse()->getStatusCode(), [401, 403])) {
                throw $ex;
            }
            $this->config->deleteTokenAccessFile();
            throw new UnauthorizedException(
                $ex->getResponse()->getBody()->getContents(),
                $ex->getResponse()->getStatusCode()
            );
        }
    }

    /**
     * @param Client $client
     * @return void
     * @throws UnauthorizedException
     */
    private function doOAuthRefresh(Client $client): void {
        $loginUri = sprintf('%s/%s/login', $this->config->getApiUrl(), Factory::API_VERSION);
        try {
            $response = $client->post(
                $loginUri,
                [
                    'form_params' => [
                        'grant_type' => 'refresh_token',
                        'refresh_token' => $this->refreshToken
                    ]
                ]
            );
            $credentials = json_decode($response->getBody()->getContents(), true);
            $this->accessToken = $credentials['access_token'];
            $this->refreshToken = $credentials['refresh_token'];
            $this->storeCredentials($credentials);
        } catch (ClientException $ex) {
            if (!in_array($ex->getResponse()->getStatusCode(), [401, 403])) {
                throw $ex;
            }
            $this->config->deleteTokenAccessFile();
            throw new UnauthorizedException(
                $ex->getResponse()->getBody()->getContents(),
                $ex->getResponse()->getStatusCode()
            );
        }
    }

    /**
     * @return bool
     */
    private function parseCredentials(): bool {
        $tokenAccessCallable = $this->config->getTokenAccessCallable();
        $credentials = call_user_func_array($tokenAccessCallable, []);
        if (!$credentials || !isset($credentials['access_token'], $credentials['refresh_token'])) {
            return false;
        }

        $this->accessToken = $credentials['access_token'];
        $this->refreshToken = $credentials['refresh_token'];

        return true;
    }

    /**
     * @param array $credentials
     * @return void
     */
    private function storeCredentials(array $credentials): void {
        $tokenRefreshCallable = $this->config->getTokenRefreshCallable();
        call_user_func_array($tokenRefreshCallable, [$credentials]);
    }
}
