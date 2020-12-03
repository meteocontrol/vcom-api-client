<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\cmms;

use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\client\vcomapi\filters\SystemCriteria;
use meteocontrol\vcomapi\model\WorkOrderForm as WorkOrderFormModel;

class WorkOrderForms extends SubEndpoint {

    /**
     * @param EndpointInterface $parent
     */
    public function __construct(EndpointInterface $parent) {
        $this->uri = '/forms';
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    /**
     * @param SystemCriteria|null $systemCriteria
     * @return WorkOrderForm[]
     */
    public function get(SystemCriteria $systemCriteria = null) {
        $systemCriteria = $systemCriteria ?? new SystemCriteria();
        $formJson = $this->api->run($this->getUri(), $systemCriteria->generateQueryString());
        $decodedJson = json_decode($formJson, true);
        return WorkOrderFormModel::deserializeArray($decodedJson['data']);
    }
}
