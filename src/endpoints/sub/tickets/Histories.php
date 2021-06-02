<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\tickets;

use meteocontrol\vcomapi\model\TicketHistory;
use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;

class Histories extends SubEndpoint {

    /**
     * @param EndpointInterface $parent
     */
    public function __construct(EndpointInterface $parent) {
        $this->uri = '/histories';
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    /**
     * @return TicketHistory[]
     */
    public function get(): array {
        $historiesJson = $this->api->run($this->getUri());
        return TicketHistory::deserializeArray($this->jsonDecode($historiesJson, true)['data']);
    }
}
