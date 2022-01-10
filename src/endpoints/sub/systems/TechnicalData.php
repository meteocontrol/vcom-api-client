<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\systems;

use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\vcomapi\model\TechnicalData as TechnicalDataModel;

class TechnicalData extends SubEndpoint {

    /**
     * @param EndpointInterface $parent
     */
    public function __construct(EndpointInterface $parent) {
        $this->uri = '/technical-data';
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    /**
     * @return TechnicalDataModel
     */
    public function get(): TechnicalDataModel {
        $technicalDataJson = $this->api->run($this->getUri());
        return TechnicalDataModel::deserialize($this->jsonDecode($technicalDataJson, true)['data']);
    }
}
