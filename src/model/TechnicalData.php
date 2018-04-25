<?php

namespace meteocontrol\vcomapi\model;

class TechnicalData extends BaseModel {

    /** @var float */
    public $nominalPower;
    /** @var float */
    public $siteArea;
    /** @var Panel[] */
    public $panels;
    /** @var InverterType[] */
    public $inverters;

    /**
     * @param array $data
     * @param null|string $name
     * @return $this
     */
    public static function deserialize(array $data, $name = null) {
        $instance = new static();

        foreach ($data as $key => $value) {
            if (is_array($value) && $key === "panels") {
                $instance->panels = Panel::deserializeArray($value);
            } elseif (is_array($value) && $key === "inverters") {
                $instance->inverters = InverterType::deserializeArray($value);
            } elseif (property_exists($instance, $key)) {
                $instance->{$key} = self::getPhpValue($value);
            }
        }
        return $instance;
    }
}
