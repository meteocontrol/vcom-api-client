<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\systems;

use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\vcomapi\model\VirtualMeter as VirtualMeterData;

class VirtualMeters extends SubEndpoint {

    /**
     * @param EndpointInterface $parent
     */
    public function __construct(EndpointInterface $parent) {
        $this->uri = "/virtual-meters";
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    /**
     * @return VirtualMeter[]
     */
    public function get() {
        $valueJson = $this->api->run($this->getUri());
        $decodedJson = json_decode($valueJson, true);
        return VirtualMeterData::deserializeArray($decodedJson["data"]);
    }
}
