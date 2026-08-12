<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\cmms;

use GuzzleHttp\RequestOptions;
use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\client\vcomapi\filters\SystemCriteria;
use meteocontrol\client\vcomapi\model\WorkOrderFormDetail;

class WorkOrderForm extends SubEndpoint {

    public function __construct(EndpointInterface $parent) {
        $this->uri = '';
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    public function get(?SystemCriteria $criteria = null): WorkOrderFormDetail {
        $options = [];
        if ($criteria) {
            $options = [RequestOptions::QUERY => $criteria->generateQueryString()];
        }
        $formJson = $this->api->get($this->getUri(), $options);
        $decodedJson = json_decode($formJson, true);
        return WorkOrderFormDetail::deserialize($decodedJson['data']);
    }
}
