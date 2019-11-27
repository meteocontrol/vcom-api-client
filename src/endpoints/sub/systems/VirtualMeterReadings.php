<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\systems;

use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\client\vcomapi\filters\MeterReadingCriteria;
use meteocontrol\vcomapi\model\VirtualMeterReading;

class VirtualMeterReadings extends SubEndpoint {

    /**
     * @param EndpointInterface $parent
     */
    public function __construct(EndpointInterface $parent) {
        $this->uri = "/readings";
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    /**
     * @param MeterReadingCriteria $criteria
     * @return array
     */
    public function get(MeterReadingCriteria $criteria) {
        $valueJson = $this->api->run($this->getUri(), $criteria->generateQueryString());
        $decodedJson = json_decode($valueJson, true);
        return VirtualMeterReading::deserializeArray($decodedJson["data"]);
    }
}
