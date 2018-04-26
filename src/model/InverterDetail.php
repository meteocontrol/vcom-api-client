<?php

namespace meteocontrol\vcomapi\model;

class InverterDetail extends BaseModel {

    /** @var string */
    public $id;
    /** @var string */
    public $model;
    /** @var string */
    public $vendor;
    /** @var string */
    public $serial;
    /** @var string */
    public $name;
    /** @var float */
    public $scaleFactor;
    /** @var string */
    public $firmware;
}
