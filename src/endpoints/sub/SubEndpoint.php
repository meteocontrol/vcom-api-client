<?php

namespace meteocontrol\client\vcomapi\endpoints\sub;

use meteocontrol\client\vcomapi\endpoints\Endpoint;

abstract class SubEndpoint extends Endpoint {
    /**
     * @return string
     */
    public function getUri(): string {
        return $this->parent->getUri() . $this->uri;
    }
}
