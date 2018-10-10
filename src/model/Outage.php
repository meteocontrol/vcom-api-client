<?php

namespace meteocontrol\vcomapi\model;

class Outage extends BaseModel {

    /** @var \DateTime */
    public $startedAt;

    /** @var \DateTime */
    public $endedAt;

    /** @var float */
    public $affectedPower;

    /** @var bool */
    public $shouldInfluenceAvailability;

    /** @var bool */
    public $shouldInfluencePr;
}
