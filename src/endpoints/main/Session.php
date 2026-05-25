<?php

namespace meteocontrol\client\vcomapi\endpoints\main;

use meteocontrol\client\vcomapi\ApiClient;
use meteocontrol\client\vcomapi\model\Session as SessionModel;

class Session extends MainEndpoint {

    public function __construct(ApiClient $apiClient) {
        $this->uri = 'session';
        $this->api = $apiClient;
    }

    public function get(): SessionModel {
        $sessionJson = $this->api->get($this->getUri());
        return SessionModel::deserialize($this->jsonDecode($sessionJson, true)['data']);
    }
}
