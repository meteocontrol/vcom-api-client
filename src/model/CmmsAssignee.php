<?php

namespace meteocontrol\vcomapi\model;

use DateTime;

class CmmsAssignee extends BaseModel {

    /** @var string */
    public $username;

    /** @var string */
    public $status;

    /** @var DateTime */
    public $statusDateAt;

    /** @var string */
    public $statusReason;

    /**
     * @param array $data
     * @return $this
     */
    public static function deserialize(array $data): self {
        $object = new static();

        foreach ($data as $key => $value) {
            if ($key === 'statusDateAt') {
                $object->{$key} = self::parseTimestamp($value);
            } elseif (property_exists($object, $key)) {
                $object->{$key} = self::getPhpValue($value);
            }
        }
        return $object;
    }

    /**
     * @param DateTime $dateTime
     * @param null|string $key
     * @return string
     */
    protected function serializeDateTime(DateTime $dateTime, string $key = null): string {
        if ($key === 'statusDateAt') {
            return $dateTime->format(DATE_ATOM);
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
}
