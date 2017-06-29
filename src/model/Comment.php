<?php

namespace meteocontrol\client\vcomapi\model;

class Comment extends BaseModel {

    /** @var int */
    public $commentId;

    /** @var \DateTime */
    public $date;

    /** @var string */
    public $comment;

    /** @var string */
    public $username;

    /** @var string */
    public $firstName;

    /** @var string */
    public $lastName;
}
