<?php

namespace meteocontrol\client\vcomapi\model;

class AttachmentFile extends BaseModel {

    /** @var int */
    public $attachmentId;

    /** @var string */
    public $filename;

    /** @var string */
    public $content;

    /**
     * @param string $filename
     * @param string $content
     */
    public function __construct($filename, $content) {
        $this->content = $this->encodeContent($content);
        $this->filename = basename($filename);
    }

    /**
     * @param array $data
     * @param null|string $name
     * @return $this
     */
    public static function deserialize(array $data, $name = null) {
        $className = get_called_class();
        $classInstance = new $className(null, null);
        foreach ($data as $key => $value) {
            if (property_exists($className, $key)) {
                $classInstance->{$key} = self::getPhpValue($value);
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
        $this->content = $this->encodeContent($content);
    }

    /**
     * @return string
     */
    public function getContent() {
        return $this->decodeContent($this->content);
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
}
