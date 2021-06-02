<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\cmms;

use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\client\vcomapi\filters\SystemCriteria;
use meteocontrol\vcomapi\model\WorkOrder as WorkOrderModel;

class WorkOrders extends SubEndpoint {

    /**
     * @param EndpointInterface $parent
     */
    public function __construct(EndpointInterface $parent) {
        $this->uri = '/workorders';
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    /**
     * @param SystemCriteria|null $systemCriteria
     * @return WorkOrderModel[]
     */
    public function get(SystemCriteria $systemCriteria = null): array {
        $systemCriteria = $systemCriteria ?? new SystemCriteria();
        $workorderJson = $this->api->run($this->getUri(), $systemCriteria->generateQueryString());
        $decodedJson = json_decode($workorderJson, true);
        return WorkOrderModel::deserializeArray($decodedJson['data']);
    }
}
