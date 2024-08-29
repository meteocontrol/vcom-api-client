<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\tickets;

use GuzzleHttp\RequestOptions;
use InvalidArgumentException;
use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\vcomapi\model\AttachmentFile;

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
    public function get(): array {
        $commentsJson = $this->api->get($this->getUri());
        return AttachmentFile::deserializeArray($this->jsonDecode($commentsJson, true)['data']);
    }

    /**
     * @param AttachmentFile $attachmentFile
     * @return array
     * @throws InvalidArgumentException
     */
    public function create(AttachmentFile $attachmentFile): array {
        if (!$attachmentFile->filename) {
            throw new InvalidArgumentException('Invalid attachment - empty file name.');
        }
        if (!$attachmentFile->content) {
            throw new InvalidArgumentException('Invalid attachment - empty file content.');
        }
        $responseBody = $this->api->post(
            $this->getUri(),
            [
                RequestOptions::BODY => json_encode(
                    [
                        'filename' => basename($attachmentFile->filename),
                        'content' => $attachmentFile->content,
                        'description' => $attachmentFile->description,
                        'metaData' => $attachmentFile->metaData,
                    ],
                    JSON_UNESCAPED_SLASHES | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS,
                ),
            ],
        );
        return $this->jsonDecode($responseBody, true)['data'];
    }
}
