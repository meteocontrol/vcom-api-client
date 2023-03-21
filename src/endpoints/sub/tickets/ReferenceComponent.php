<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\tickets;

use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\client\vcomapi\filters\ReferenceComponentCriteria;
use meteocontrol\client\vcomapi\model\YieldLoss;

class ReferenceComponent extends SubEndpoint {

    public function __construct(EndpointInterface $parent) {
        $this->uri = '/reference-component';
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    public function get(ReferenceComponentCriteria $criteria): YieldLoss {
        $json = $this->api->run($this->getUri(), $criteria->generateQueryString());
        return YieldLoss::deserialize($this->jsonDecode($json, true)['data']);
    }
}
