<?php

namespace meteocontrol\client\vcomapi\model;

abstract class BaseModel {
    /**
     * @param array $data
     * @param null|string $name
     * @return $this
     */
    public static function deserialize(array $data, $name = null) {
        $className = get_called_class();
        $classInstance = new $className();
        foreach ($data as $key => $value) {
            if (property_exists($className, $key)) {
                $classInstance->{$key} = self::getPhpValue($value);
            }
        }
        return $classInstance;
    }

    /**
     * @param array $decodedJsonArray
     * @return array
     */
    public static function deserializeArray(array $decodedJsonArray) {
        $objects = [];
        $className = get_called_class();
        foreach ($decodedJsonArray as $item) {
            $objects[] = $className::deserialize($item);
        }
        return $objects;
    }

    /**
     * @param string | integer | float $value
     * @return \DateTime | string | integer | float
     */
    protected static function getPhpValue($value) {
        if (self::isSimpleDateString($value)) {
            return \DateTime::createFromFormat('Y-m-d H:i:s', $value . ' 00:00:00');
        } elseif (self::isRFC3339DateString($value)) {
            return \DateTime::createFromFormat(\DateTime::RFC3339, $value);
        } elseif (self::isISO8601WithoutOffsetDateString($value)) {
            return \DateTime::createFromFormat(\DateTime::RFC3339, $value . 'Z');
        } else {
            return $value;
        }
    }

    /**
     * @param $dateString
     * @return bool
     */
    private static function isSimpleDateString($dateString) {
        return preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', $dateString) == 1;
    }

    /**
     * @param $dateString
     * @return bool
     */
    private static function isRFC3339DateString($dateString)
    {
        return \DateTime::createFromFormat(\DateTime::RFC3339, $dateString);
    }

    /**
     * @param $dateString
     * @return \DateTime
     */
    private static function isISO8601WithoutOffsetDateString($dateString) {
        return \DateTime::createFromFormat(\DateTime::RFC3339, $dateString . 'Z');
    }
}
