<?php

declare(strict_types=1);

namespace meteocontrol\client\vcomapi\endpoints\sub\systems\system;

use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\client\vcomapi\endpoints\sub\systems\system\satellite\Irradiance;

class Satellite extends SubEndpoint {

    public function __construct(EndpointInterface $parent) {
        $this->uri = '/satellite';
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    public function irradiance(): Irradiance {
        return new Irradiance($this);
    }
}
