<?php

declare(strict_types=1);

namespace meteocontrol\client\vcomapi\model;

use DateTime;

class SimulationValue extends BaseModel {

    /** @var DateTime */
    public $timestamp;

    /** @var float */
    public $max;

    /** @var float */
    public $min;

    /** @var float */
    public $expected;
}
