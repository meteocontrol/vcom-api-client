<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\systems;

use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\AbbreviationId;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\client\vcomapi\endpoints\sub\systems\calculations\Simulation;
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
     * @param string|array $abbreviationId
     * @return DeviceAbbreviation
     */
    public function abbreviation($abbreviationId) {
        $abbreviationId = is_array($abbreviationId) ? implode(',', $abbreviationId) : $abbreviationId;
        $abbreviations = new Abbreviations($this);
        $abbreviationIdEndpoint = new AbbreviationId($abbreviations, $abbreviationId);
        return new DeviceAbbreviation($abbreviationIdEndpoint);
    }

    /**
     * @return Bulk
     */
    public function bulk() {
        return new Bulk($this);
    }

    /**
     * @return Simulation
     */
    public function simulation() {
        return new Simulation($this);
    }
}
