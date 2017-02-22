<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\systems;

use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\client\vcomapi\model\Meter;

class Meters extends SubEndpoint {
    /**
     * @param EndpointInterface $parent
     */
    public function __construct(EndpointInterface $parent) {
        $this->uri = '/meters';
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    /**
     * @return Meter[]
     */
    public function get() {
        $invertersJson = $this->api->run($this->getUri());
        $decodedJson = json_decode($invertersJson, true);
        return Meter::deserializeArray($decodedJson['data']);
    }

    /**
     * @return Bulk
     */
    public function bulk() {
        return new Bulk($this);
    }
}
