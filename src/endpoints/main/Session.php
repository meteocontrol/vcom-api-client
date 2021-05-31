<?php

namespace meteocontrol\client\vcomapi\endpoints\main;

use meteocontrol\vcomapi\model\Session as SessionModel;
use meteocontrol\client\vcomapi\ApiClient;

class Session extends MainEndpoint {

    /**
     * @param ApiClient $apiClient
     */
    public function __construct(ApiClient $apiClient) {
        $this->uri = 'session';
        $this->api = $apiClient;
    }

    /**
     * @return SessionModel
     */
    public function get(): SessionModel {
        $sessionJson = $this->api->run($this->getUri());
        return SessionModel::deserialize($this->jsonDecode($sessionJson, true)['data']);
    }
}
