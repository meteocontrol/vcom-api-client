<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\systems\system\forecasts\SpecificEnergy;

use GuzzleHttp\RequestOptions;
use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\client\vcomapi\filters\MeasurementsCriteria;
use meteocontrol\vcomapi\model\MeasurementValue;

class SpecificEnergy extends SubEndpoint {

    /**
     * @param EndpointInterface $parent
     */
    public function __construct(EndpointInterface $parent) {
        $this->uri = '/specific-energy';
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    /**
     * @param MeasurementsCriteria $measurementCriteria
     * @return array
     */
    public function get(MeasurementsCriteria $measurementCriteria): array {
        $valueJson = $this->api->get(
            $this->getUri(),
            [RequestOptions::QUERY => $measurementCriteria->generateQueryString()],
        );
        $decodedJson = json_decode($valueJson, true);
        return MeasurementValue::deserializeArray($decodedJson['data']);
    }
}
