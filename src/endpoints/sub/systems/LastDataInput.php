<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\systems;

use GuzzleHttp\RequestOptions;
use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\client\vcomapi\filters\SystemCriteria;
use meteocontrol\client\vcomapi\model\LastDataInput as LastDataInputModel;

class LastDataInput extends SubEndpoint {

    public function __construct(EndpointInterface $parent) {
        $this->uri = '/last-data-input';
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    public function get(?SystemCriteria $criteria = null): LastDataInputModel {
        $options = [];
        if ($criteria) {
            $options = [RequestOptions::QUERY => $criteria->generateQueryString()];
        }
        $json = $this->api->get($this->getUri(), $options);
        return LastDataInputModel::deserialize($this->jsonDecode($json, true)['data']);
    }
}
