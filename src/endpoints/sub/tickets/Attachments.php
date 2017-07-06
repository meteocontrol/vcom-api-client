<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\tickets;

use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\client\vcomapi\model\AttachmentFile;

class Attachments extends SubEndpoint {

    /**
     * @param EndpointInterface $parent
     */
    public function __construct(EndpointInterface $parent) {
        $this->uri = '/attachments';
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    /**
     * @return array[]
     */
    public function get() {
        $commentsJson = $this->api->run($this->getUri());
        $decodedJson = json_decode($commentsJson, true);
        return $decodedJson['data'];
    }

    /**
     * @param AttachmentFile $attachmentFile
     * @return array
     */
    public function create(AttachmentFile $attachmentFile) {
        if (!$attachmentFile->filename || !$attachmentFile->content) {
            throw new \InvalidArgumentException('attachment is invalid!');
        }
        $responseBody = $this->api->run(
            $this->getUri(),
            null,
            json_encode(
                [
                    'filename' => basename($attachmentFile->filename),
                    'content' => $attachmentFile->content
                ],
                79
            ),
            'POST'
        );
        $decodedJson = json_decode($responseBody, true);
        return $decodedJson['data'];
    }
}
