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
        $this->uri = '';
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    /**
     * @return VirtualMeterDetail
     */
    public function get(): VirtualMeterDetail {
        $json = $this->api->get($this->getUri());
        return VirtualMeterDetail::deserialize($this->jsonDecode($json, true)['data']);
    }

    /**
     * @return VirtualMeterReadings
     */
    public function readings(): VirtualMeterReadings {
        return new VirtualMeterReadings($this);
    }
}
