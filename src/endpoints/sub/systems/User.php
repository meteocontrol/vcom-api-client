<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\systems;

use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\vcomapi\model\UserDetail;

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
     */
    public function get(): UserDetail {
        $userDetailJson = $this->api->run($this->getUri());
        return UserDetail::deserialize($this->jsonDecode($userDetailJson, true)['data']);
    }
}
