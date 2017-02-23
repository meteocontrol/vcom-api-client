<?php

namespace meteocontrol\client\vcomapi\model;

class CommentDetail extends BaseModel {

    /** @var int */
    public $commentId;

    /** @var \DateTime */
    public $date;

    /** @var string */
    public $comment;

    /** @var string */
    public $username;

    /**
     * @return bool
     */
    public function isValid() {
        return !empty($this->comment);
    }
}
