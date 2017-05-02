<?php

namespace meteocontrol\client\vcomapi;

use GuzzleHttp\Client;
use meteocontrol\client\vcomapi\endpoints\main\Systems;
use meteocontrol\client\vcomapi\endpoints\main\Tickets;
use meteocontrol\client\vcomapi\endpoints\sub\systems\System;
use meteocontrol\client\vcomapi\endpoints\sub\systems\SystemId;
use meteocontrol\client\vcomapi\endpoints\sub\tickets\TicketId;
use Psr\Http\Message\ResponseInterface;

class ApiClient {

    /** @var Config */
    private $config;
    /** @var Client */
    private $client;

    /**
     * @param Config $config
     * @param Client $client
     */
    public function __construct(Config $config, Client $client) {
        $this->config = $config;
        $this->client = $client;
    }

    /**
     * @param string $username
     * @param string $password
     * @param string $apiKey
     * @return ApiClient
     */
    public static function get($username, $password, $apiKey) {
        $config = new Config();
        $config->setApiUsername($username);
        $config->setApiPassword($password);
        $config->setApiKey($apiKey);
        $config->validate();
        $client = Factory::getHttpClient($config);

        return new ApiClient(
            $config,
            $client
        );
    }

    /**
     * @return Systems
     */
    public function systems() {
        return new Systems($this);
    }

    /**
     * @param string $systemKey
     * @return System
     */
    public function system($systemKey) {
        $systems = new Systems($this);
        $systemIdEndpoint = new SystemId($systems, $systemKey);
        $systemEndpoint = new System($systemIdEndpoint);
        return $systemEndpoint;
    }


    /**
     * @return Tickets
     */
    public function tickets() {
        return new Tickets($this);
    }

    /**
     * @param int $ticketId
     * @return endpoints\sub\tickets\Ticket
     */
    public function ticket($ticketId) {
        $tickets = new Tickets($this);
        $ticketIdEndpoint = new TicketId($tickets, $ticketId);
        $ticketEndpoint = new \meteocontrol\client\vcomapi\endpoints\sub\tickets\Ticket($ticketIdEndpoint);
        return $ticketEndpoint;
    }

    /**
     * @param string $uri
     * @param array|string $queryParams
     * @param string $body
     * @param string $method
     * @return mixed
     * @throws ApiClientException
     */
    public function run($uri, $queryParams = null, $body = null, $method = 'GET') {
        /** @var $response ResponseInterface */
        $response = null;
        $options = [
            'query' => $queryParams ?: null,
            'body' => $body ?: null,
            'headers' => [
                'Accept-Encoding' => 'gzip, deflate',
                'Content-Type' => 'application/json'
            ],
        ];

        switch (strtoupper($method)) {
            case 'GET':
                $response = $this->client->get($uri, $options);
                break;
            case 'DELETE':
                $response = $this->client->delete($uri, $options);
                break;
            case 'PATCH':
                $response = $this->client->patch($uri, $options);
                break;
            case 'POST':
                $response = $this->client->post($uri, $options);
                break;
            default:
                throw new ApiClientException('Unacceptable HTTP method ' . $method);
        }

        if ($response->getHeaderLine('X-RateLimit-Remaining-Minute') == '1') {
            $requestTime = date_create_from_format('D, d M Y H:i:s \G\M\T', $response->getHeaderLine('Date'));
            $resetTime = date_create_from_format(
                'D, d M Y H:i:s \G\M\T',
                $response->getHeaderLine('X-RateLimit-Reset-Minute')
            );
            usleep(($resetTime->getTimestamp() - $requestTime->getTimestamp()) * 1000000);
        }

        return $response->getBody()->getContents();
    }
}
