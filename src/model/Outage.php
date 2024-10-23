<?php

namespace meteocontrol\client\vcomapi\model;

use DateTime;

class Outage extends BaseModel {

    /** @var DateTime */
    public $startedAt;

    /** @var DateTime|null */
    public $endedAt;

    /** @var float */
    public $affectedPower;

    /** @var bool */
    public $shouldInfluenceAvailability;

    /** @var bool */
    public $shouldInfluencePr;

    /** @var string[] */
    public $components;
}
