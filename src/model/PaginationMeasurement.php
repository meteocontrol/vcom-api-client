<?php

namespace meteocontrol\vcomapi\model;

class PaginationMeasurement extends BaseModel {

    /** @var int */
    public $pageSize;
    /** @var int */
    public $totalCount;
    /** @var PaginationLinks */
    public $links;
    /** @var Measurement[] */
    public $data;

    /**
     * @param array $data
     * @return $this
     */
    public static function deserialize(array $data) {
        $object = new static();

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                if ($key === 'data') {
                    $object->{$key} = Measurement::deserializeArray($value);
                } elseif ($key === 'links') {
                    $object->{$key} = PaginationLinks::deserialize($value);
                }
            } else {
                $object->{$key} = self::getPhpValue($value);
            }
        }
        return $object;
    }
}
