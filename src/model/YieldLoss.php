<?php

namespace meteocontrol\client\vcomapi\model;

class YieldLoss extends BaseModel {

    /** @var float */
    public $result;

    /** @var float */
    public $realLostYield;

    /** @var float */
    public $totalCompensation;

    /** @var string|null */
    public $comment;

    /**
     * @return bool
     */
    public function isValid(): bool {
        return !empty($this->realLostYield) || !empty($this->comment);
    }
}
