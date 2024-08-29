<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\systems;

use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\vcomapi\model\Battery;

class Batteries extends SubEndpoint {

    /**
     * @param EndpointInterface $parent
     */
    public function __construct(EndpointInterface $parent) {
        $this->uri = '/batteries';
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    /**
     * @return Battery[]
     */
    public function get(): array {
        $json = $this->api->get($this->getUri());
        return Battery::deserializeArray($this->jsonDecode($json, true)['data']);
    }

    /**
     * @return Bulk
     */
    public function bulk(): Bulk {
        return new Bulk($this);
    }
}
