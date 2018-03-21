<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\systems;

use meteocontrol\vcomapi\model\TechnicalData as TechnicalDataModel;
use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;

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
    public function get() {
        $technicalDataJson = $this->api->run($this->getUri());
        $decodedJson = json_decode($technicalDataJson, true);
        return TechnicalDataModel::deserialize($decodedJson['data']);
    }
}
