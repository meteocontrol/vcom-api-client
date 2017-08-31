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
     * @return AttachmentFile[]
     */
    public function get() {
        $commentsJson = $this->api->run($this->getUri());
        $decodedJson = json_decode($commentsJson, true);
        return AttachmentFile::deserializeArray($decodedJson['data']);
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
                    'content' => $this->encodeContent($attachmentFile->content),
                    'description' => $attachmentFile->description
                ],
                79
            ),
            'POST'
        );
        $decodedJson = json_decode($responseBody, true);
        return $decodedJson['data'];
    }

    /**
     * @param string $content
     * @return string | null
     */
    private function encodeContent($content) {
        if (!$content) {
            return null;
        }
        return 'data:' . "image/jpeg" . ';base64,' . base64_encode($content);
    }
}
