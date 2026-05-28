<?php

namespace meteocontrol\client\vcomapi\model;

use DateTime;

class YieldLossCalculation extends YieldLoss {

    /** @var string */
    public $model;

    /** @var DateTime */
    public $from;

    /** @var DateTime */
    public $until;
}
