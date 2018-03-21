<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\systems;

use meteocontrol\vcomapi\model\UserDetail;
use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;

class User extends SubEndpoint {

    /**
     * @param EndpointInterface $parent
     */
    public function __construct(EndpointInterface $parent) {
        $this->uri = '';
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    /**
     * @return UserDetail
     * @throws \meteocontrol\client\vcomapi\ApiClientException
     */
    public function get() {
        $userDetailJson = $this->api->run($this->getUri());
        $decodedJson = json_decode($userDetailJson, true);
        return UserDetail::deserialize($decodedJson['data']);
    }
}
