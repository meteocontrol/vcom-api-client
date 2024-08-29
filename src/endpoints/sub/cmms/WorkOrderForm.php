<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\cmms;

use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\vcomapi\model\WorkOrderFormDetail;

class WorkOrderForm extends SubEndpoint {

    /**
     * @param EndpointInterface $parent
     */
    public function __construct(EndpointInterface $parent) {
        $this->uri = '';
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    /**
     * @return WorkOrderFormDetail
     */
    public function get(): WorkOrderFormDetail {
        $formJson = $this->api->get($this->getUri());
        $decodedJson = json_decode($formJson, true);
        return WorkOrderFormDetail::deserialize($decodedJson['data']);
    }
}
