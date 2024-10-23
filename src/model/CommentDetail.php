<?php

namespace meteocontrol\client\vcomapi\model;

use DateTime;

class CommentDetail extends BaseModel {

    /** @var int */
    public $commentId;
    /** @var DateTime */
    public $createdAt;
    /** @var string */
    public $comment;
    /** @var string */
    public $username;
    /** @var string */
    public $firstName;
    /** @var string */
    public $lastName;

    /**
     * @return bool
     */
    public function isValid(): bool {
        return !empty($this->comment);
    }
}
