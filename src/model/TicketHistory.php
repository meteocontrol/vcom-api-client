<?php

namespace meteocontrol\vcomapi\model;

use DateTime;

class TicketHistory extends BaseModel {

    /** @var DateTime */
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
