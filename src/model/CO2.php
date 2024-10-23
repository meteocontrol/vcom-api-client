<?php
namespace meteocontrol\client\vcomapi\model;

use DateTime;

class CO2 extends BaseModel {

    /** @var DateTime */
    public $timestamp;
    /** @var float|null */
    public $value;
}
