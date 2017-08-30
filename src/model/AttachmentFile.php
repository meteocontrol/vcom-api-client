<?php

namespace meteocontrol\client\vcomapi\model;

class AttachmentFile extends BaseModel {

    /** @var int */
    private $attachmentId;

    /** @var string */
    private $filename;

    /** @var string */
    private $encodedContent;

    /** @var string */
    private $description;

    /** @var int */
    private $creatorId;

    /** @var \DateTime */
    private $created;

    /**
     * @param string | null $filename
     * @param string | null $content
     * @param string | null $description
     */
    public function __construct($filename = null, $content = null, $description = null) {
        $this->encodedContent = $this->encodeContent($content);
        $this->filename = $filename ? basename($filename) : null;
        $this->description = $description;
    }

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
                $classInstance->{self::getSetterMethodName($key)}(self::getPhpValue($value));
            } elseif ($key == "content") {
                $classInstance->{"setEncodedContent"}(self::getPhpValue($value));
            }
        }
        return $classInstance;
    }

    /**
     * @param int $id
     */
    public function setAttachmentId($id) {
        $this->attachmentId = $id;
    }

    /**
     * @return int
     */
    public function getAttachmentId() {
        return $this->attachmentId;
    }

    /**
     * @param string $encodedContent
     */
    public function setContent($encodedContent) {
        $this->encodedContent = $this->encodeContent($encodedContent);
    }

    /**
     * @return string
     */
    public function getContent() {
        return $this->decodeContent($this->encodedContent);
    }

    /**
     * @param string $encodedContent
     */
    public function setEncodedContent($encodedContent) {
        $this->encodedContent = $encodedContent;
    }

    /**
     * @return string
     */
    public function getEncodedContent() {
        return $this->encodedContent;
    }

    /**
     * @param string $filename
     */
    public function setFilename($filename) {
        $this->filename = basename($filename);
    }

    /**
     * @return string
     */
    public function getFilename() {
        return $this->filename;
    }

    /**
     * @param string $description
     */
    public function setDescription($description) {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * @param int $creatorId
     */
    public function setCreatorId($creatorId) {
        $this->creatorId = $creatorId;
    }

    /**
     * @return int
     */
    public function getCreatorId() {
        return $this->creatorId;
    }

    /**
     * @return \DateTime
     */
    public function getCreated() {
        return $this->created;
    }

    /**
     * @param \DateTime $created
     */
    public function setCreated(\DateTime $created) {
        $this->created = $created;
    }

    /**
     * @param string $content
     * @return string | null
     */
    private function encodeContent($content) {
        if (!$content) {
            return null;
        }
        return 'data:' . "image/jpeg" . ';base64,' . base64_encode($content);
    }

    /**
     * @param string $encodedContent
     * @return string
     */
    private function decodeContent($encodedContent) {
        list(, $data) = explode(';', $encodedContent);
        list(, $data) = explode(',', $data);
        return base64_decode($data);
    }

    /**
     * @param string $key
     * @return string
     */
    private static function getSetterMethodName($key) {
        return "set" . ucfirst($key);
    }
}
