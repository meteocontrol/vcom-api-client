<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\tickets;

use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;

class Peak extends SubEndpoint {

    public function __construct(EndpointInterface $parent) {
        $this->uri = '/peak';
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    public function gridOperator(): GridOperator {
        return new GridOperator($this);
    }

    public function directMarketing(): DirectMarketing {
        return new DirectMarketing($this);
    }
}
