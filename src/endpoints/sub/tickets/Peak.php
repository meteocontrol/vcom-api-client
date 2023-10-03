<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\tickets;

use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\client\vcomapi\filters\YieldLossesCriteria;
use meteocontrol\client\vcomapi\model\YieldLoss;

class Peak extends SubEndpoint {

    public function __construct(EndpointInterface $parent) {
        $this->uri = '/peak';
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    /**
     * @deprecated It is scheduled to be removed on 2023-10-31.
     */
    public function get(YieldLossesCriteria $criteria): YieldLoss {
        $json = $this->api->run($this->getUri(), $criteria->generateQueryString());
        return YieldLoss::deserialize($this->jsonDecode($json, true)['data']);
    }

    public function gridOperator(): GridOperator {
        return new GridOperator($this);
    }

    public function directMarketing(): DirectMarketing {
        return new DirectMarketing($this);
    }
}
