<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\tickets;

use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\vcomapi\model\AttachmentFile;

class Attachment extends SubEndpoint {

    /**
     * @param EndpointInterface $parent
     */
    public function __construct(EndpointInterface $parent) {
        $this->uri = '';
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    /**
     * @return AttachmentFile
     */
    public function get(): AttachmentFile {
        $meterJson = $this->api->run($this->getUri());
        return AttachmentFile::deserialize($this->jsonDecode($meterJson, true)['data']);
    }
}
