<?php

declare(strict_types=1);

namespace meteocontrol\vcomapi\model;

use DateTime;

class ToleranceValue extends BaseModel {

    /** @var DateTime */
    public $timestamp;

    /** @var float */
    public $max;

    /** @var float */
    public $min;

    /** @var float */
    public $expected;
}
