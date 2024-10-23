<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\systems;

use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\client\vcomapi\model\VirtualMeter as VirtualMeterData;

class VirtualMeters extends SubEndpoint {

    /**
     * @param EndpointInterface $parent
     */
    public function __construct(EndpointInterface $parent) {
        $this->uri = '/virtual-meters';
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    /**
     * @return VirtualMeter[]
     */
    public function get(): array {
        $json = $this->api->get($this->getUri());
        return VirtualMeterData::deserializeArray($this->jsonDecode($json, true)['data']);
    }
}
