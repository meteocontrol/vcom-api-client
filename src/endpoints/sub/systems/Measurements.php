<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\systems;

use GuzzleHttp\RequestOptions;
use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\client\vcomapi\filters\MeasurementsCriteria;
use meteocontrol\client\vcomapi\model\Measurement;

class Measurements extends SubEndpoint {

    /**
     * @param EndpointInterface $parent
     */
    public function __construct(EndpointInterface $parent) {
        $this->uri = '/measurements';
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    /**
     * @param MeasurementsCriteria $criteria
     * @return Measurement[]
     */
    public function get(MeasurementsCriteria $criteria): array {
        $measurementsJson = $this->api->get(
            $this->getUri(),
            [RequestOptions::QUERY => $criteria->generateQueryString()]
        );
        return Measurement::deserializeArray($this->jsonDecode($measurementsJson, true)['data']);
    }
}
