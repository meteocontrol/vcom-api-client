<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\cmms;

use meteocontrol\client\vcomapi\ApiClient;
use meteocontrol\client\vcomapi\endpoints\main\MainEndpoint;

class Cmms extends MainEndpoint {

    public function __construct(ApiClient $apiClient) {
        $this->uri = 'cmms';
        $this->api = $apiClient;
    }

    public function systems(): Systems {
        return new Systems($this);
    }

    public function workOrders(): WorkOrders {
        return new WorkOrders($this);
    }

    public function workOrder(int $workOrderId): WorkOrder {
        $workOrders = new WorkOrders($this);
        $workOrderIdEndpoint = new WorkOrderId($workOrders, $workOrderId);
        return new WorkOrder($workOrderIdEndpoint);
    }

    public function siteAccess(): SiteAccess {
        return new SiteAccess($this);
    }
}
