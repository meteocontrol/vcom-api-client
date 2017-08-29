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
     * @param string | null $filename
     * @param string | null $content
     * @param string | null $description
     */
    public function __construct($filename = null, $content = null, $description = null) {
        $this->content = $this->encodeContent($content);
        $this->filename = $filename ? basename($filename) : null;
        $this->description = $description;
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
