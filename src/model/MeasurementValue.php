<?php

namespace meteocontrol\vcomapi\model;

use DateTime;

class MeasurementValue extends BaseModel {

    /** @var DateTime */
    public $timestamp;
    /** @var string */
    public $value;
}
