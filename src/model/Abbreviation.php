<?php

namespace meteocontrol\vcomapi\model;

class Abbreviation extends BaseModel {

    /** @var string */
    public $aggregation;
    /** @var int */
    public $precision;
    /** @var string */
    public $description;
    /** @var string */
    public $unit;
}
