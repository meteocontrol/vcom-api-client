<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\tickets;

use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\client\vcomapi\model\AttachmentFile;

class Attachment extends SubEndpoint {

    public function __construct(EndpointInterface $parent) {
        $this->uri = '';
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    public function get(): AttachmentFile {
        $attachmentJson = $this->api->get($this->getUri());
        return AttachmentFile::deserialize($this->jsonDecode($attachmentJson, true)['data']);
    }
}
