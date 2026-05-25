<?php

namespace meteocontrol\client\vcomapi\endpoints\main;

use GuzzleHttp\RequestOptions;
use meteocontrol\client\vcomapi\ApiClient;
use meteocontrol\client\vcomapi\endpoints\sub\AbbreviationId;
use meteocontrol\client\vcomapi\endpoints\sub\systems\Abbreviation as SystemsAbbreviation;
use meteocontrol\client\vcomapi\endpoints\sub\systems\Abbreviations as SystemsAbbreviations;
use meteocontrol\client\vcomapi\filters\SystemCriteria;
use meteocontrol\client\vcomapi\model\SystemWithTags;

class Systems extends MainEndpoint {

    public function __construct(ApiClient $apiClient) {
        $this->uri = 'systems';
        $this->api = $apiClient;
    }

    /**
     * @param SystemCriteria|null $criteria
     * @return SystemWithTags[]
     */
    public function get(?SystemCriteria $criteria = null): array {
        $options = [];
        if ($criteria) {
            $options = [RequestOptions::QUERY => $criteria->generateQueryString()];
        }
        $systemsJson = $this->api->get($this->getUri(), $options);
        return SystemWithTags::deserializeArray($this->jsonDecode($systemsJson, true)['data']);
    }

    public function abbreviations(): SystemsAbbreviations {
        return new SystemsAbbreviations($this);
    }

    /**
     * @param string|array $abbreviationId
     * @return SystemsAbbreviation
     */
    public function abbreviation($abbreviationId): SystemsAbbreviation {
        $abbreviationId = is_array($abbreviationId) ? implode(',', $abbreviationId) : $abbreviationId;
        $abbreviations = new SystemsAbbreviations($this);
        $abbreviationIdEndpoint = new AbbreviationId($abbreviations, $abbreviationId);
        return new SystemsAbbreviation($abbreviationIdEndpoint);
    }
}
