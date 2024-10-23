<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\cmms;

use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\client\vcomapi\model\WorkOrderDetail;

class WorkOrder extends SubEndpoint {

    /**
     * @param EndpointInterface $parent
     */
    public function __construct(EndpointInterface $parent) {
        $this->uri = '';
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    /**
     * @return WorkOrderDetail
     */
    public function get(): WorkOrderDetail {
        $workorderJson = $this->api->get($this->getUri());
        $decodedJson = json_decode($workorderJson, true);
        return WorkOrderDetail::deserialize($decodedJson['data']);
    }

    /**
     * @return WorkOrderForms
     */
    public function forms(): WorkOrderForms {
        return new WorkOrderForms($this);
    }

    /**
     * @param int $formId
     * @return WorkOrderForm
     */
    public function form(int $formId): WorkOrderForm {
        $workOrderForms = new WorkOrderForms($this);
        $formIdEndpoint = new FormId($workOrderForms, $formId);
        return new WorkOrderForm($formIdEndpoint);
    }
}
