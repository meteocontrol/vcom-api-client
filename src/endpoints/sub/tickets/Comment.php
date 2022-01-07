<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\tickets;

use InvalidArgumentException;
use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\vcomapi\model\CommentDetail;

class Comment extends SubEndpoint {

    /**
     * @param EndpointInterface $parent
     */
    public function __construct(EndpointInterface $parent) {
        $this->uri = '';
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    /**
     * @return CommentDetail
     */
    public function get(): CommentDetail {
        $meterJson = $this->api->run($this->getUri());
        return CommentDetail::deserialize($this->jsonDecode($meterJson, true)['data']);
    }

    /**
     * @param CommentDetail $commentDetail
     * @return void
     * @throws InvalidArgumentException
     */
    public function update(CommentDetail $commentDetail): void {
        if (!$commentDetail || !$commentDetail->isValid()) {
            throw new InvalidArgumentException('Comment is invalid!');
        }
        $this->api->run(
            $this->getUri(),
            null,
            json_encode(['comment' => $commentDetail->comment]),
            'PATCH'
        );
    }

    /**
     * @return void
     */
    public function delete(): void {
        $this->api->run(
            $this->getUri(),
            null,
            null,
            'DELETE'
        );
    }
}
