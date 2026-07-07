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
     * @deprecated
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

    /** @var CmmsAdditionalAssignee[] */
    public $additionalAssignees;

    public static function deserialize(array $data): self {
        $object = new static();

        foreach ($data as $key => $value) {
            if (in_array($key, ['dueAt', 'createdAt', 'completedAt', 'lastChangedAt'])) {
                $object->{$key} = self::parseTimestamp($value);
            } elseif ($key === "dueDate") {
                $object->{$key} = self::parseDate($value);
            } elseif ($key === "assignee" && is_array($value)) {
                $object->assignee = CmmsAssignee::deserialize($value);
            } elseif ($key === "additionalAssignees" && is_array($value)) {
                $object->additionalAssignees = CmmsAdditionalAssignee::deserializeArray($value);
            } elseif (property_exists($object, $key)) {
                $object->{$key} = self::getPhpValue($value);
            }
        }

        return $object;
    }

    protected function serializeDateTime(DateTimeInterface $dateTime, ?string $key = null): string {
        if (in_array($key, ['dueAt', 'createdAt', 'completedAt', 'lastChangedAt'])) {
            return $dateTime->format(DATE_ATOM);
        } elseif ($key === 'dueDate') {
            return $dateTime->format('Y-m-d');
        }
        return parent::serializeDateTime($dateTime);
    }

    private static function parseTimestamp(?string $value): ?DateTime {
        return $value ? DateTime::createFromFormat(DATE_ATOM, $value) : null;
    }

    private static function parseDate(?string $value): ?DateTime {
        return $value ? DateTime::createFromFormat('Y-m-d', $value, new DateTimeZone('UTC')) : null;
    }
}
