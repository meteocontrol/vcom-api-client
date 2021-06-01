<?php

namespace meteocontrol\vcomapi\model;

use DateTime;
use JsonSerializable;

abstract class BaseModel implements JsonSerializable {

    /**
     * @param array $data
     * @return $this
     */
    public static function deserialize(array $data) {
        $object = new static();
        foreach ($data as $key => $value) {
            if (property_exists($object, $key)) {
                $object->{$key} = static::getPhpValue($value);
            }
        }
        return $object;
    }

    /**
     * @param array $decodedJsonArray
     * @return array
     */
    public static function deserializeArray(array $decodedJsonArray): array {
        $objects = [];
        foreach ($decodedJsonArray as $item) {
            $objects[] = static::deserialize($item);
        }
        return $objects;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array {
        $values = get_object_vars($this);

        foreach ($values as $key => $value) {
            if ($value instanceof DateTime) {
                $values[$key] = $this->serializeDateTime($value, $key);
            }
        }

        return $values;
    }

    /**
     * @param DateTime $dateTime
     * @param null|string $key
     * @return string
     */
    protected function serializeDateTime(DateTime $dateTime, string $key = null): string {
        return $dateTime->format(DateTime::ATOM);
    }

    /**
     * @param array | string | int | float | null $value
     * @return DateTime | string | int | float | null
     */
    protected static function getPhpValue($value) {
        if (self::isRFC3339DateString($value)) {
            return DateTime::createFromFormat(DateTime::RFC3339, $value);
        } else {
            return $value;
        }
    }

    /**
     * @param array|string|null $dateString
     * @return bool|DateTime
     */
    private static function isRFC3339DateString($dateString) {
        return is_string($dateString) && DateTime::createFromFormat(DateTime::RFC3339, $dateString);
    }
}
