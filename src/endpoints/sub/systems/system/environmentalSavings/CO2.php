<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\systems\system\environmentalSavings;

use GuzzleHttp\RequestOptions;
use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\client\vcomapi\filters\MeasurementsCriteria;
use meteocontrol\client\vcomapi\model\CO2 as CO2Model;

class CO2 extends SubEndpoint {

    public function __construct(EndpointInterface $parent) {
        $this->uri = '/co2';
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    /**
     * @param MeasurementsCriteria $criteria
     * @return CO2Model[]
     */
    public function get(MeasurementsCriteria $criteria): array {
        $json =  $this->api->get($this->getUri(), [RequestOptions::QUERY => $criteria->generateQueryString()]);
        return CO2Model::deserializeArray($this->jsonDecode($json, true)['data']);
    }
}
