<?php

namespace meteocontrol\vcomapi\model;

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

    /** @var int */
    public $id;

    /** @var string */
    public $systemKey;

    /** @var string */
    public $designation;

    /** @var string */
    public $summary;

    /** @var \DateTime */
    public $createdAt;

    /** @var \DateTime */
    public $lastChangedAt;

    /** @var \DateTime */
    public $rectifiedAt;

    /** @var string */
    public $assignee;

    /** @var string */
    public $status;

    /** @var int */
    public $causeId;

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
    public function isValid() {
        return !empty($this->systemKey)
            && !empty($this->designation)
            && !empty($this->createdAt);
    }

    public static function deserialize(array $data, $name = null) {
        $object = new static();

        foreach ($data as $key => $value) {
            if (in_array($key, ['createdAt', 'lastChangedAt', 'rectifiedAt'])) {
                $object->{$key} = self::parseTimestamp($value);
            } elseif ($key === "outage" && is_array($value)) {
                $object->outage = Outage::deserialize($value);
            } elseif (property_exists($object, $key)) {
                $object->{$key} = self::getPhpValue($value);
            }
        }
        return $object;
    }

    /**
     * @param \DateTime $dateTime
     * @param null|string $key
     * @return string
     */
    protected function serializeDateTime(\DateTime $dateTime, $key = null) {
        if (in_array($key, ['createdAt', 'lastChangedAt', 'rectifiedAt'])) {
            return $dateTime->format('Y-m-d H:i:s');
        }
        return parent::serializeDateTime($dateTime);
    }

    /**
     * @param string $value
     * @return \DateTime
     */
    private static function parseTimestamp($value) {
        if (self::isDateString($value)) {
            return \DateTime::createFromFormat('Y-m-d H:i:s', $value);
        } else {
            return self::getPhpValue($value);
        }
    }

    /**
     * @param string $dateString
     * @return bool
     */
    private static function isDateString($dateString) {
        return \DateTime::createFromFormat('Y-m-d H:i:s', $dateString);
    }
}
