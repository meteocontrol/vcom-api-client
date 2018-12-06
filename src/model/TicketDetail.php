<?php

namespace meteocontrol\vcomapi\model;

class TicketDetail extends Ticket {

    /** @var Outage|null */
    public $outage;

    /**
     * @param array $data
     * @return TicketDetail
     */
    public static function deserialize(array $data) {
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
