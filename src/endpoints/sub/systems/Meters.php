<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\systems;

use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\vcomapi\model\Meter;

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
    public function get(): array {
        $json = $this->api->run($this->getUri());
        return Meter::deserializeArray($this->jsonDecode($json, true)['data']);
    }

    /**
     * @return Bulk
     */
    public function bulk(): Bulk {
        return new Bulk($this);
    }
}
