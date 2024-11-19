<?php
declare(strict_types=1);

namespace meteocontrol\client\vcomapi\model;

class TrackerDetail extends BaseModel {

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
    public $vendor;
    /** @var string */
    public $model;
    /** @var string */
    public $firmware;
}
