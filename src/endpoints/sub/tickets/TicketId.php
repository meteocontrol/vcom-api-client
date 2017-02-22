<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\tickets;

use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;

class TicketId extends SubEndpoint {
    /**
     * @param EndpointInterface $parent
     * @param string $id
     */
    public function __construct(EndpointInterface $parent, $id) {
        $this->uri = '/' . $id;
        $this->parent = $parent;
        $this->api = $parent->getApiClient();
    }
}
