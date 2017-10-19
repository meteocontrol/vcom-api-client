<?php

namespace meteocontrol\client\vcomapi\model;

class TicketHistory extends BaseModel {
    /**
     * @var \DateTime
     * @deprecated
     */
    public $timestamp;
    /** @var \DateTime */
    public $createdAt;
    /** @var string */
    public $action;
    /** @var string */
    public $personInCharge;
    /** @var null|int|string */
    public $from;
    /** @var null|int|string */
    public $to;
}
