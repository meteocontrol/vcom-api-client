<?php

namespace meteocontrol\client\vcomapi\model;

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

    /** @var string */
    public $scaleFactor;
}
