<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\systems;

use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\AbbreviationId;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\client\vcomapi\endpoints\sub\systems\system\calculations\Abbreviation as DeviceAbbreviation;

class Calculations extends SubEndpoint {

    /**
     * @param EndpointInterface $parent
     */
    public function __construct(EndpointInterface $parent) {
        $this->uri = '/calculations';
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    /**
     * @return Abbreviations
     */
    public function abbreviations() {
        return new Abbreviations($this);
    }

    /**
     * @param string $abbreviationId
     * @return DeviceAbbreviation
     */
    public function abbreviation($abbreviationId) {
        $abbreviations = new Abbreviations($this);
        $abbreviationIdEndpoint = new AbbreviationId($abbreviations, $abbreviationId);
        $abbreviationEndpoint = new DeviceAbbreviation($abbreviationIdEndpoint);
        return $abbreviationEndpoint;
    }

    /**
     * @return Bulk
     */
    public function bulk() {
        return new Bulk($this);
    }
}
