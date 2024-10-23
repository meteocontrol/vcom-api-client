<?php
namespace meteocontrol\client\vcomapi\model;

use DateTime;

class VirtualMeterReading extends BaseModel {

    /** @var string */
    public $id;
    /** @var string */
    public $type;
    /** @var DateTime */
    public $timestamp;
    /** @var float */
    public $value;
}
