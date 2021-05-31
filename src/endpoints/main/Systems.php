<?php

namespace meteocontrol\client\vcomapi\endpoints\main;

use meteocontrol\vcomapi\model\System as SystemModel;
use meteocontrol\client\vcomapi\ApiClient;
use meteocontrol\client\vcomapi\endpoints\sub\AbbreviationId;
use meteocontrol\client\vcomapi\endpoints\sub\systems\Abbreviation as SystemsAbbreviation;
use meteocontrol\client\vcomapi\endpoints\sub\systems\Abbreviations as SystemsAbbreviations;

class Systems extends MainEndpoint {

    /**
     * @param ApiClient $apiClient
     */
    public function __construct(ApiClient $apiClient) {
        $this->uri = 'systems';
        $this->api = $apiClient;
    }

    /**
     * @return SystemModel[]
     */
    public function get() {
        $systemsJson = $this->api->run($this->getUri());
        return SystemModel::deserializeArray($this->jsonDecode($systemsJson, true)['data']);
    }

    /**
     * @return SystemsAbbreviations
     */
    public function abbreviations() {
        return new SystemsAbbreviations($this);
    }

    /**
     * @param string|array $abbreviationId
     * @return SystemsAbbreviation
     */
    public function abbreviation($abbreviationId) {
        $abbreviationId = is_array($abbreviationId) ? implode(',', $abbreviationId) : $abbreviationId;
        $abbreviations = new SystemsAbbreviations($this);
        $abbreviationIdEndpoint = new AbbreviationId($abbreviations, $abbreviationId);
        $abbreviationEndpoint = new SystemsAbbreviation($abbreviationIdEndpoint);
        return $abbreviationEndpoint;
    }
}
