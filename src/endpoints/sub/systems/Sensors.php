<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\systems;

use meteocontrol\vcomapi\model\Sensor;
use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;

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
    public function get() {
        $json = $this->api->run($this->getUri());
        return Sensor::deserializeArray($this->jsonDecode($json, true)['data']);
    }

    /**
     * @return Bulk
     */
    public function bulk() {
        return new Bulk($this);
    }
}
