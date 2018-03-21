<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\tickets;

use meteocontrol\vcomapi\model\AttachmentFile;
use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;

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
    public function get() {
        $meterJson = $this->api->run($this->getUri());
        $decodedJson = json_decode($meterJson, true);
        return AttachmentFile::deserialize($decodedJson['data']);
    }
}
