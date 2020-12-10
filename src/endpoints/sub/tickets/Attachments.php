<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\tickets;

use meteocontrol\vcomapi\model\AttachmentFile;
use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;

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
     * @return AttachmentFile[]
     */
    public function get() {
        $commentsJson = $this->api->run($this->getUri());
        return AttachmentFile::deserializeArray($this->jsonDecode($commentsJson, true)['data']);
    }

    /**
     * @param AttachmentFile $attachmentFile
     * @return array
     */
    public function create(AttachmentFile $attachmentFile) {
        if (!$attachmentFile->filename) {
            throw new \InvalidArgumentException('Invalid attachment - empty file name.');
        }
        if (!$attachmentFile->content) {
            throw new \InvalidArgumentException('Invalid attachment - empty file content.');
        }
        $responseBody = $this->api->run(
            $this->getUri(),
            null,
            json_encode(
                [
                    'filename' => basename($attachmentFile->filename),
                    'content' => $attachmentFile->content,
                    'description' => $attachmentFile->description,
                    'metaData' => $attachmentFile->metaData
                ],
                79
            ),
            'POST'
        );
        return $this->jsonDecode($responseBody, true)['data'];
    }
}
