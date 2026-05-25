<?php

namespace meteocontrol\client\vcomapi\model;

class MpptInput extends BaseModel {

    /** @var PanelModule */
    public $module;

    /** @var string */
    public $type;

    /** @var int */
    public $modulesPerString;

    /** @var int */
    public $stringCount;

    public static function deserialize(array $data): self {
        $object = new static();

        foreach ($data as $key => $value) {
            if (is_array($value) && $key === "module") {
                $object->module = PanelModule::deserialize($value);
            } elseif (property_exists($object, $key)) {
                $object->{$key} = self::getPhpValue($value);
            }
        }
        return $object;
    }

    public static function deserializeArray(array $decodedJsonArray): array {
        return array_map(static function ($item) {
            return static::deserialize($item);
        }, $decodedJsonArray);
    }
}
