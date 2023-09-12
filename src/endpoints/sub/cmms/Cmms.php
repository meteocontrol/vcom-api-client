<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\cmms;

use meteocontrol\client\vcomapi\ApiClient;
use meteocontrol\client\vcomapi\endpoints\main\MainEndpoint;

class Cmms extends MainEndpoint {

    /**
     * @param ApiClient $apiClient
     */
    public function __construct(ApiClient $apiClient) {
        $this->uri = 'cmms';
        $this->api = $apiClient;
    }

    /**
     * @return Systems
     */
    public function systems(): Systems {
        return new Systems($this);
    }

    /**
     * @return WorkOrders
     */
    public function workOrders(): WorkOrders {
        return new WorkOrders($this);
    }

    /**
     * @param int $workOrderId
     * @return WorkOrder
     */
    public function workOrder(int $workOrderId): WorkOrder {
        $workOrders = new WorkOrders($this);
        $workOrderIdEndpoint = new WorkOrderId($workOrders, $workOrderId);
        return new WorkOrder($workOrderIdEndpoint);
    }
}
