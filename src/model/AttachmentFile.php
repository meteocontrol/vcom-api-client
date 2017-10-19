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

    /**
     * @var \DateTime
     * @deprecated
     */
    public $created;

    /** @var \DateTime */
    public $createdAt;
}
