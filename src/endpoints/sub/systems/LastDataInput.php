<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\systems;

use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\vcomapi\model\LastDataInput as LastDataInputModel;

class LastDataInput extends SubEndpoint {

    public function __construct(EndpointInterface $parent) {
        $this->uri = '/last-data-input';
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    public function get(): LastDataInputModel {
        $json = $this->api->run($this->getUri());
        return LastDataInputModel::deserialize($this->jsonDecode($json, true)['data']);
    }
}
