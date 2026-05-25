<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\cmms;

use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;

class WorkOrderId extends SubEndpoint {

    public function __construct(EndpointInterface $parent, int $workOrderId) {
        $this->uri = '/' . $workOrderId;
        $this->parent = $parent;
        $this->api = $parent->getApiClient();
    }
}
