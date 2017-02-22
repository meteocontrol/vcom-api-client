<?php

namespace meteocontrol\client\vcomapi\model;

class Comment extends BaseModel {
    /** @var integer */
    public $commentId;
    /** @var \DateTime */
    public $date;
    /** @var string */
    public $comment;
    /** @var string */
    public $username;
}
