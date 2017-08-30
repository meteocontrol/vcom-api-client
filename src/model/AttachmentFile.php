<?php

namespace meteocontrol\client\vcomapi\model;

class AttachmentFile extends BaseModel {

    /** @var int */
    private $attachmentId;

    /** @var string */
    private $filename;

    /** @var string */
    private $content;

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
        $this->setContent($content);
        $this->setFilename($filename);
        $this->setDescription($description);
    }

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
                $classInstance->{self::getSetterMethodName($key)}(self::getPhpValue($value));
            }
        }
        return $classInstance;
    }

    /**
     * @param int $id
     */
    public function setId($id) {
        $this->attachmentId = $id;
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->attachmentId;
    }

    /**
     * @param string $content
     */
    public function setContent($content) {
        $this->content = $content;
        $this->encodedContent = $this->base64Encode($content);
    }

    /**
     * @return string
     */
    public function getContent() {
        return $this->content;
    }

    /**
     * @param string $encodedContent
     */
    public function setEncodedContent($encodedContent) {
        $this->content = $this->base64Decode($encodedContent);
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
        $this->filename = $filename ? basename($filename) : null;
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
     * @param string $type
     * @return string | null
     */
    private function base64Encode($content, $type = "image/jpeg") {
        if (!$content) {
            return null;
        }
        return 'data:' . $type . ';base64,' . base64_encode($content);
    }

    /**
     * @param string $encodedContent
     * @return string
     */
    private function base64Decode($encodedContent) {
        list(, $data) = explode(';', $encodedContent);
        list(, $data) = explode(',', $data);
        return base64_decode($data);
    }

    /**
     * @param string $key
     * @return string
     */
    private static function getSetterMethodName($key) {
        $param = $key;
        switch ($key) {
            case "content":
                $param = "encodedContent";
                break;
            case "attachmentId":
                $param = "id";
                break;
        }
        return "set" . ucfirst($param);
    }
}
