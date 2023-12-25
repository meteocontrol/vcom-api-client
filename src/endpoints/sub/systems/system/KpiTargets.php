<?php

declare(strict_types=1);

namespace meteocontrol\client\vcomapi\endpoints\sub\systems\system;

use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\client\vcomapi\endpoints\sub\systems\system\kpiTargets\KpiTarget;

class KpiTargets extends SubEndpoint {

    public function __construct(EndpointInterface $parent) {
        $this->uri = '/kpi-targets';
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    public function pr(): KpiTarget {
        return new KpiTarget($this, '/pr');
    }

    public function availability(): KpiTarget {
        return new KpiTarget($this, '/availability');
    }
}
