<?php

namespace meteocontrol\vcomapi\model;

use DateTime;

class Ticket extends BaseModel {

    const REPORT_TYPE_NO = 'no';
    const REPORT_TYPE_DETAIL = 'detail';
    const REPORT_TYPE_SUMMARY = 'summary';

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

    /** @var string */
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
    public $assignee;

    /** @var string */
    public $status;

    /** @var int|null */
    public $causeId;

    /** @var string */
    public $cause;

    /** @var string */
    public $priority;

    /** @var string */
    public $includeInReports;

    /** @var bool */
    public $fieldService;

    /** @var string */
    public $severity;

    /** @var string */
    public $description;

    /** @var Outage|null */
    public $outage;

    /**
     * @return bool
     */
    public function isValid(): bool {
        return !empty($this->systemKey)
            && !empty($this->designation)
            && !empty($this->createdAt);
    }

    /**
     * @param array $data
     * @param null|string $name
     * @return $this
     */
    public static function deserialize(array $data, $name = null): self {
        $object = new static();

        foreach ($data as $key => $value) {
            if ($key === "outage" && is_array($value)) {
                $object->outage = Outage::deserialize($value);
            } elseif (property_exists($object, $key)) {
                $object->{$key} = self::getPhpValue($value);
            }
        }
        return $object;
    }
}
