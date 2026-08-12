<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\cmms;

use GuzzleHttp\RequestOptions;
use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\client\vcomapi\filters\SystemCriteria;
use meteocontrol\client\vcomapi\model\WorkOrderDetail;

class WorkOrder extends SubEndpoint {

    public function __construct(EndpointInterface $parent) {
        $this->uri = '';
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    public function get(?SystemCriteria $criteria = null): WorkOrderDetail {
        $options = [];
        if ($criteria) {
            $options = [RequestOptions::QUERY => $criteria->generateQueryString()];
        }
        $workorderJson = $this->api->get($this->getUri(), $options);
        $decodedJson = json_decode($workorderJson, true);
        return WorkOrderDetail::deserialize($decodedJson['data']);
    }

    public function forms(): WorkOrderForms {
        return new WorkOrderForms($this);
    }

    public function form(int $formId): WorkOrderForm {
        $workOrderForms = new WorkOrderForms($this);
        $formIdEndpoint = new FormId($workOrderForms, $formId);
        return new WorkOrderForm($formIdEndpoint);
    }
}
