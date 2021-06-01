<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\systems;

use meteocontrol\vcomapi\model\PictureFile;
use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;

class Picture extends SubEndpoint {

    /**
     * @param EndpointInterface $parent
     */
    public function __construct(EndpointInterface $parent) {
        $this->uri = '/picture';
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    /**
     * @return PictureFile
     */
    public function get(): PictureFile {
        $pictureJson = $this->api->run($this->getUri());
        return PictureFile::deserialize($this->jsonDecode($pictureJson, true)['data']);
    }
}
