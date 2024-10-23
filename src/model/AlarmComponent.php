<?php

declare(strict_types=1);

namespace meteocontrol\client\vcomapi\model;

class AlarmComponent extends BaseModel {

    /** @var string */
    public $type;

    /** @var string|null */
    public $id;

    /** @var string|null */
    public $name;
}
