<?php

namespace meteocontrol\client\vcomapi\model;

use DateTime;

class TicketOverview extends BaseModel {

    const STATUS_OPEN = 'open';
    const STATUS_CLOSED = 'closed';
    const STATUS_DELETED = 'deleted';
    const STATUS_ASSIGNED = 'assigned';
    const STATUS_INPROGRESS = 'inProgress';

    const SEVERITY_NORMAL = 'normal';
    const SEVERITY_HIGH = 'high';
    const SEVERITY_CRITICAL = 'critical';

    const PRIORITY_LOW = 'low';
    const PRIORITY_NORMAL = 'normal';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_URGENT = 'urgent';

    /** @var int */
    public $id;

    /** @var string */
    public $systemKey;

    /** @var string */
    public $designation;

    /** @var string */
    public $summary;

    /** @var DateTime */
    public $createdAt;

    /** @var DateTime */
    public $lastChangedAt;

    /** @var DateTime */
    public $rectifiedAt;

    /** @var string */
    public $status;

    /** @var string */
    public $priority;

    /** @var bool */
    public $fieldService;

    /** @var string */
    public $severity;

    /** @var string */
    public $description;

    /**
     * @return bool
     */
    public function isValid(): bool {
        return !empty($this->systemKey)
            && !empty($this->designation)
            && !empty($this->createdAt);
    }
}
