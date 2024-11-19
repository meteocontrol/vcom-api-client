<?php

namespace meteocontrol\client\vcomapi\model;

class MeterDetail extends BaseModel {

    /** @var string */
    public $id;
    /** @var string */
    public $name;
    /** @var string|null */
    public $uid;
    /**
     * @var string
     * @deprecated
     */
    public $address;
    /** @var string */
    public $firmware;
}
