<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\systems;

use meteocontrol\vcomapi\model\Battery;
use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;

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
    public function get() {
        $json = $this->api->run($this->getUri());
        $decodedJson = json_decode($json, true);
        return Battery::deserializeArray($decodedJson['data']);
    }

    /**
     * @return Bulk
     */
    public function bulk() {
        return new Bulk($this);
    }
}
