<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\systems;

use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\vcomapi\model\Sensor;

class Sensors extends SubEndpoint {

    /**
     * @param EndpointInterface $parent
     */
    public function __construct(EndpointInterface $parent) {
        $this->uri = '/sensors';
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    /**
     * @return Sensor[]
     */
    public function get(): array {
        $json = $this->api->get($this->getUri());
        return Sensor::deserializeArray($this->jsonDecode($json, true)['data']);
    }

    /**
     * @return Bulk
     */
    public function bulk(): Bulk {
        return new Bulk($this);
    }
}
