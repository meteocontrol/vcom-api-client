<?php

namespace meteocontrol\client\vcomapi\endpoints\main;

use meteocontrol\client\vcomapi\endpoints\Endpoint;

abstract class MainEndpoint extends Endpoint {
    /**
     * @return string
     */
    final public function getUri() {
        return $this->uri;
    }
}
