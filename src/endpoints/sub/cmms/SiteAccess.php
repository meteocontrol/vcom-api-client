<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\cmms;

use GuzzleHttp\RequestOptions;
use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\client\vcomapi\filters\SiteAccessCriteria;
use meteocontrol\client\vcomapi\model\SiteAccess as SiteAccessModel;

class SiteAccess extends SubEndpoint {

    /**
     * @param EndpointInterface $parent
     */
    public function __construct(EndpointInterface $parent) {
        $this->uri = '/site-access';
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    /**
     * @param SiteAccessCriteria $criteria
     * @return SiteAccessModel[]
     */
    public function get(SiteAccessCriteria $criteria): array {
        $siteAccessJson = $this->api->get(
            $this->getUri(),
            [RequestOptions::QUERY => $criteria->generateQueryString()],
        );
        $decodedJson = json_decode($siteAccessJson, true);
        return SiteAccessModel::deserializeArray($decodedJson['data']);
    }
}
