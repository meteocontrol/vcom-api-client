<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\tickets;

use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\vcomapi\model\ServicePartner as ServicePartnerModel;

class ServicePartner extends SubEndpoint {

    /**
     * @param EndpointInterface $parent
     */
    public function __construct(EndpointInterface $parent) {
        $this->uri = '/service-partner';
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    /**
     * @return ServicePartnerModel
     */
    public function get() {
        $commentsJson = $this->api->run($this->getUri());
        $decodedJson = json_decode($commentsJson, true);
        return ServicePartnerModel::deserialize($decodedJson['data']);
    }
}
