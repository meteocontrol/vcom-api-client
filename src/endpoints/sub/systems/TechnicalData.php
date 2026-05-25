<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\systems;

use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\client\vcomapi\model\TechnicalData as TechnicalDataModel;

class TechnicalData extends SubEndpoint {

    public function __construct(EndpointInterface $parent) {
        $this->uri = '/technical-data';
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    public function get(): TechnicalDataModel {
        $technicalDataJson = $this->api->get($this->getUri());
        return TechnicalDataModel::deserialize($this->jsonDecode($technicalDataJson, true)['data']);
    }

    public function lastDataInput(): LastDataInput {
        return new LastDataInput($this);
    }
}
