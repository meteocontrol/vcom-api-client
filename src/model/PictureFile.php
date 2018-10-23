<?php

namespace meteocontrol\vcomapi\model;

class PictureFile extends BaseModel {

    /** @var int */
    public $id;
    /** @var string */
    public $filename;
    /** @var string */
    public $content;
    /** @var string */
    public $type;
}
