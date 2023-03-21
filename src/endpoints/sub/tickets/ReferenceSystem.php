<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\tickets;

use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\client\vcomapi\filters\ReferenceSystemCriteria;
use meteocontrol\client\vcomapi\model\YieldLoss;

class ReferenceSystem extends SubEndpoint {

    public function __construct(EndpointInterface $parent) {
        $this->uri = '/reference-system';
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    public function get(ReferenceSystemCriteria $criteria): YieldLoss {
        $json = $this->api->run($this->getUri(), $criteria->generateQueryString());
        return YieldLoss::deserialize($this->jsonDecode($json, true)['data']);
    }
}
