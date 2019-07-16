<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\systems;

use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;

class DeviceId extends SubEndpoint {

    /**
     * @param EndpointInterface $parent
     * @param string $id
     */
    public function __construct(EndpointInterface $parent, string $id) {
        $this->uri = '/' . $id;
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }
}
