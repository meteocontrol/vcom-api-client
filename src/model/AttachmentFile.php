<?php

namespace meteocontrol\vcomapi\model;

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
    public $createdAt;
    /** @var string */
    public $metaData;
}
