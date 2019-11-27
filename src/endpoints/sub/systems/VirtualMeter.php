<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\systems;

use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\vcomapi\model\VirtualMeterDetail;

class VirtualMeter extends SubEndpoint {

    /**
     * @param EndpointInterface $parent
     */
    public function __construct(EndpointInterface $parent) {
        $this->uri = "";
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    /**
     * @return VirtualMeterDetail
     */
    public function get() {
        $valueJson = $this->api->run($this->getUri());
        $decodedJson = json_decode($valueJson, true);
        return VirtualMeterDetail::deserialize($decodedJson["data"]);
    }

    /**
     * @return VirtualMeterReadings
     */
    public function readings() {
        return new VirtualMeterReadings($this);
    }
}
