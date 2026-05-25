<?php

namespace meteocontrol\client\vcomapi;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use meteocontrol\client\vcomapi\endpoints\main\Alarms;
use meteocontrol\client\vcomapi\endpoints\main\Session;
use meteocontrol\client\vcomapi\endpoints\main\Systems;
use meteocontrol\client\vcomapi\endpoints\main\Tickets;
use meteocontrol\client\vcomapi\endpoints\sub\alarms\Alarm;
use meteocontrol\client\vcomapi\endpoints\sub\cmms\Cmms;
use meteocontrol\client\vcomapi\endpoints\sub\systems\System;
use meteocontrol\client\vcomapi\endpoints\sub\systems\SystemId;
use meteocontrol\client\vcomapi\endpoints\sub\tickets\Ticket;
use meteocontrol\client\vcomapi\endpoints\sub\tickets\TicketId;
use meteocontrol\client\vcomapi\handlers\AuthorizationHandlerInterface;
use Psr\Http\Message\ResponseInterface;

class ApiClient {

    /** @var Client */
    private $client;

    /** @var AuthorizationHandlerInterface */
    private $authorizationHandler;

    public function __construct(Client $client, AuthorizationHandlerInterface $authorizationHandler) {
        $this->client = $client;
        $this->authorizationHandler = $authorizationHandler;
    }

    public static function make(string $username, string $password, string $apiKey): self {
        $config = new Config();
        $config->setApiUsername($username);
        $config->setApiPassword($password);
        $config->setApiKey($apiKey);
        $config->validate();
        $client = Factory::getHttpClient($config);

        return new ApiClient(
            $client,
            Factory::getAuthorizationHandler($config)
        );
    }

    public function systems(): Systems {
        return new Systems($this);
    }

    public function system(string $systemKey): System {
        $systems = new Systems($this);
        $systemIdEndpoint = new SystemId($systems, $systemKey);
        return new System($systemIdEndpoint);
    }

    public function tickets(): Tickets {
        return new Tickets($this);
    }

    public function ticket(string $ticketId): Ticket {
        $tickets = new Tickets($this);
        $ticketIdEndpoint = new TicketId($tickets, $ticketId);
        return new Ticket($ticketIdEndpoint);
    }

    public function alarms(): Alarms {
        return new Alarms($this);
    }

    public function alarm(int $alarmId): Alarm {
        return new Alarm($this->alarms(), $alarmId);
    }

    public function session(): Session {
        return new Session($this);
    }

    public function cmms(): Cmms {
        return new Cmms($this);
    }

    public function get(string $uri, array $options = []): ?string {
        return $this->run($uri, 'GET', $options);
    }

    public function put(string $uri, array $options = []): ?string {
        return $this->run($uri, 'PUT', $options);
    }

    public function post(string $uri, array $options = []): ?string {
        return $this->run($uri, 'POST', $options);
    }

    public function patch(string $uri, array $options = []): ?string {
        return $this->run($uri, 'PATCH', $options);
    }

    public function delete(string $uri, array $options = []): ?string {
        return $this->run($uri, 'DELETE', $options);
    }

    private function run(string $uri, string $method, array $options): ?string {
        /** @var $response ResponseInterface */
        $response = null;
        $requestOptions = $this->getRequestOptions($options);

        try {
            $response = $this->sendRequest($uri, $method, $requestOptions);
        } catch (ClientException $ex) {
            if ($ex->getResponse()->getStatusCode() === 401 && $ex->getMessage() !== 'Invalid API key') {
                $this->authorizationHandler->handleUnauthorizedException($ex, $this->client);
                $response = $this->retryRequestWithNewToken($uri, $method, $requestOptions);
            } else {
                throw $ex;
            }
        }

        if ($response->getHeaderLine('X-RateLimit-Remaining-Minute') == '1') {
            $requestTime = date_create_from_format('D, d M Y H:i:s \G\M\T', $response->getHeaderLine('Date'));
            $resetTime = date_create_from_format(
                'D, d M Y H:i:s \G\M\T',
                $response->getHeaderLine('X-RateLimit-Reset-Minute')
            );
            usleep(($resetTime->getTimestamp() - $requestTime->getTimestamp() + 2) * 1000000);
        }

        return $response->getBody()->getContents();
    }

    private function getRequestOptions(array $options): array {
        $headers = ['accept-encoding' => 'gzip, deflate'];

        $options[RequestOptions::HEADERS] = isset($options[RequestOptions::HEADERS])
            ? array_merge($headers, array_change_key_case($options[RequestOptions::HEADERS]))
            : $headers;

        return $this->authorizationHandler->appendAuthorizationHeader($this->client, $options);
    }

    /**
     * @param string $uri
     * @param string $method
     * @param array $options
     * @return ResponseInterface
     */
    private function sendRequest(string $uri, string $method, array $options): ResponseInterface {
        switch ($method) {
            case 'GET':
                return $this->client->get($uri, $options);
            case 'PUT':
                return $this->client->put($uri, $options);
            case 'POST':
                return $this->client->post($uri, $options);
            case 'PATCH':
                return $this->client->patch($uri, $options);
            case 'DELETE':
                return $this->client->delete($uri, $options);
        }
    }

    /**
     * @param string $uri
     * @param string $method
     * @param array $options
     * @return ResponseInterface
     * @throws UnauthorizedException
     */
    private function retryRequestWithNewToken(string $uri, string $method, array $options): ResponseInterface {
        try {
            return $this->sendRequest($uri, $method, $options);
        } catch (ClientException $ex) {
            throw new UnauthorizedException($ex->getMessage(), $ex->getCode());
        }
    }
}
