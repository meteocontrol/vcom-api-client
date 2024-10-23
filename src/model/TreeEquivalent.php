<?php
namespace meteocontrol\client\vcomapi\model;

use DateTime;

class TreeEquivalent extends BaseModel {

    /** @var DateTime */
    public $timestamp;
    /** @var int|null */
    public $value;
}
