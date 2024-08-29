<?php

declare(strict_types=1);

namespace meteocontrol\client\vcomapi\endpoints\sub\tickets;

use meteocontrol\client\vcomapi\endpoints\EndpointInterface;

class ManualSetpoint extends YieldLossesSetpointEndpoint {

    public function __construct(EndpointInterface $parent) {
        parent::__construct($parent, '/manual');
    }
}