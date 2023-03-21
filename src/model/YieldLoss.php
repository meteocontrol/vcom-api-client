<?php

namespace meteocontrol\client\vcomapi\model;

class YieldLoss extends BaseModel {

    /** @var float */
    public $result;

    /** @var float */
    public $realLostYield;

    /** @var float */
    public $totalCompensation;

    /** @var string */
    public $comment;
}
