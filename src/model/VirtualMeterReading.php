<?php
namespace meteocontrol\vcomapi\model;

use DateTime;

class VirtualMeterReading extends BaseModel {

    /** @var int */
    public $id;
    /** @var string */
    public $type;
    /** @var DateTime */
    public $timestamp;
    /** @var float */
    public $value;
}
