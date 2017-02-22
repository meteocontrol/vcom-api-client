<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\systems;

use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\client\vcomapi\model\Sensor;

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
        $invertersJson = $this->api->run($this->getUri());
        $decodedJson = json_decode($invertersJson, true);
        return Sensor::deserializeArray($decodedJson['data']);
    }

    /**
     * @return Bulk
     */
    public function bulk() {
        return new Bulk($this);
    }
}
