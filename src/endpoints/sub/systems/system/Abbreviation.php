<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\systems\system;

use meteocontrol\vcomapi\model\Abbreviation as AbbreviationModel;
use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;

class Abbreviation extends SubEndpoint {

    /**
     * @param EndpointInterface $parent
     */
    public function __construct(EndpointInterface $parent) {
        $this->uri = '';
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    /**
     * @return AbbreviationModel
     */
    public function get() {
        $abbreviationJson = $this->api->run($this->getUri());
        $decodedJson = json_decode($abbreviationJson, true);
        return AbbreviationModel::deserialize($decodedJson['data']);
    }

    /**
     * @return Measurements
     */
    public function measurements() {
        return new Measurements($this);
    }
}
