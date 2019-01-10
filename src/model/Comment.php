<?php

namespace meteocontrol\vcomapi\model;

class Comment extends BaseModel {

    /** @var int */
    public $commentId;
    /** @var \DateTime */
    public $createdAt;
    /** @var string */
    public $comment;
    /** @var string */
    public $username;
    /** @var string */
    public $firstName;
    /** @var string */
    public $lastName;
}
