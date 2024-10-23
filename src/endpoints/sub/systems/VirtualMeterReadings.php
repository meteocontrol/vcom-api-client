<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\systems;

use GuzzleHttp\RequestOptions;
use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\client\vcomapi\filters\MeterReadingCriteria;
use meteocontrol\client\vcomapi\model\VirtualMeterReading;

class VirtualMeterReadings extends SubEndpoint {

    /**
     * @param EndpointInterface $parent
     */
    public function __construct(EndpointInterface $parent) {
        $this->uri = '/readings';
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    /**
     * @param MeterReadingCriteria $criteria
     * @return array
     */
    public function get(MeterReadingCriteria $criteria): array {
        $json = $this->api->get($this->getUri(), [RequestOptions::QUERY => $criteria->generateQueryString()]);
        return VirtualMeterReading::deserializeArray($this->jsonDecode($json, true)['data']);
    }
}
