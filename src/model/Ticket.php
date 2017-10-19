<?php

namespace meteocontrol\client\vcomapi\model;

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
    public $date;

    /** @var \DateTime */
    public $lastChange;

    /** @var \DateTime */
    public $rectifiedOn;

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

    /**
     * @return bool
     */
    public function isValid() {
        return !empty($this->systemKey) && !empty($this->designation) && !empty($this->date);
    }

    public static function deserialize(array $data, $name = null) {
        $className = get_called_class();
        $classInstance = new $className();
        foreach ($data as $key => $value) {
            if (in_array($key, ['date', 'lastChange', 'rectifiedOn'])) {
                $classInstance->{$key} = self::parseTimestamp($value);
            } elseif (property_exists($className, $key)) {
                $classInstance->{$key} = self::getPhpValue($value);
            }
        }
        return $classInstance;
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
