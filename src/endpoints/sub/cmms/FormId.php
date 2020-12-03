<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\cmms;

use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;

class FormId extends SubEndpoint {

    /**
     * @param EndpointInterface $parent
     * @param int $formId
     */
    public function __construct(EndpointInterface $parent, int $formId) {
        $this->uri = '/' . $formId;
        $this->parent = $parent;
        $this->api = $parent->getApiClient();
    }
}
