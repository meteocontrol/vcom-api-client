<?php

namespace meteocontrol\client\vcomapi\model;

use DateTime;
use DateTimeInterface;
use DateTimeZone;

class WorkOrderDetail extends BaseModel {

    /** @var int */
    public $workOrderId;

    /** @var int */
    public $ticketId;

    /** @var string */
    public $systemKey;

    /** @var string */
    public $title;

    /** @var string */
    public $description;

    /** @var string */
    public $status;

    /**
     * @var DateTime
     * @deprecated deprecated
     */
    public $dueAt;

    /** @var DateTime */
    public $dueDate;

    /** @var DateTime */
    public $createdAt;

    /** @var DateTime */
    public $completedAt;

    /** @var DateTime */
    public $lastChangedAt;

    /** @var CmmsAssignee */
    public $assignee;

    /**
     * @param array $data
     * @return $this
     */
    public static function deserialize(array $data): self {
        $object = new static();

        foreach ($data as $key => $value) {
            if (in_array($key, ['dueAt', 'createdAt', 'completedAt', 'lastChangedAt'])) {
                $object->{$key} = self::parseTimestamp($value);
            } elseif ($key === "dueDate") {
                $object->{$key} = self::parseDate($value);
            } elseif (is_array($value) && $key === "assignee") {
                $object->assignee = CmmsAssignee::deserialize($value);
            } elseif (property_exists($object, $key)) {
                $object->{$key} = self::getPhpValue($value);
            }
        }
        return $object;
    }

    /**
     * @param DateTimeInterface $dateTime
     * @param null|string $key
     * @return string
     */
    protected function serializeDateTime(DateTimeInterface $dateTime, $key = null): string {
        if (in_array($key, ['dueAt', 'createdAt', 'completedAt', 'lastChangedAt'])) {
            return $dateTime->format(DATE_ATOM);
        } elseif ($key === 'dueDate') {
            return $dateTime->format('Y-m-d');
        }
        return parent::serializeDateTime($dateTime);
    }

    /**
     * @param string|null $value
     * @return DateTime|null
     */
    private static function parseTimestamp(?string $value): ?DateTime {
        return $value ? DateTime::createFromFormat(DATE_ATOM, $value) : null;
    }

    /**
     * @param string|null $value
     * @return DateTime|null
     */
    private static function parseDate(?string $value): ?DateTime {
        return $value ? DateTime::createFromFormat('Y-m-d', $value, new DateTimeZone('UTC')) : null;
    }
}
