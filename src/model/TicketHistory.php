<?php

namespace meteocontrol\client\vcomapi\model;

class TicketHistory extends BaseModel {
    /** @var \DateTime */
    public $timestamp;
    /** @var string */
    public $action;
    /** @var int */
    public $personInCharge;
    /** @var null|int|string */
    public $from;
    /** @var null|int|string */
    public $to;
}
