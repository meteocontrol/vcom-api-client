<?php
declare(strict_types=1);

namespace meteocontrol\client\vcomapi\model;

class Tracker extends BaseModel {

    /** @var string */
    public $id;
    /** @var string */
    public $name;
    /** @var string|null */
    public $uid;
}
