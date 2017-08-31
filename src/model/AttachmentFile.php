<?php

namespace meteocontrol\client\vcomapi\model;

class AttachmentFile extends BaseModel {

    /** @var int */
    public $attachmentId;

    /** @var string */
    public $filename;

    /** @var string */
    public $content;

    /** @var string */
    public $description;

    /** @var int */
    public $creatorId;

    /** @var \DateTime */
    public $created;

    /**
     * @param array $data
     * @param string | null $name
     * @return $this
     */
    public static function deserialize(array $data, $name = null) {
        $className = get_called_class();
        $classInstance = new $className();
        foreach ($data as $key => $value) {
            if (property_exists($className, $key)) {
                if ($key == "content") {
                    $classInstance->{$key} = self::decode($value);
                } else {
                    $classInstance->{$key} = self::getPhpValue($value);
                }
            }
        }
        return $classInstance;
    }

    /**
     * @param string $encodedContent
     * @return bool|string
     */
    private static function decode($encodedContent) {
        list(, $data) = explode(';', $encodedContent);
        list(, $data) = explode(',', $data);
        return base64_decode($data);
    }
}
