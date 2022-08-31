<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\systems\system\environmentalSavings;

use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\client\vcomapi\filters\MeasurementsCriteria;
use meteocontrol\vcomapi\model\TreeEquivalent as TreeEquivalentsModel;

class TreeEquivalents extends SubEndpoint {

    public function __construct(EndpointInterface $parent) {
        $this->uri = '/tree';
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    /**
     * @param MeasurementsCriteria $criteria
     * @return TreeEquivalentsModel[]
     */
    public function get(MeasurementsCriteria $criteria): array {
        $json =  $this->api->run($this->getUri(), $criteria->generateQueryString());
        return TreeEquivalentsModel::deserializeArray($this->jsonDecode($json, true)['data']);
    }
}
