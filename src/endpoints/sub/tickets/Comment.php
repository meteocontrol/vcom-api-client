<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\tickets;

use meteocontrol\vcomapi\model\CommentDetail;
use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;

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
    public function get() {
        $meterJson = $this->api->run($this->getUri());
        $decodedJson = json_decode($meterJson, true);
        return CommentDetail::deserialize($decodedJson['data']);
    }

    /**
     * @param CommentDetail $commentDetail
     */
    public function update(CommentDetail $commentDetail) {
        if (!$commentDetail || !$commentDetail->isValid()) {
            throw new \InvalidArgumentException('Comment is invalid!');
        }
        $this->api->run(
            $this->getUri(),
            null,
            json_encode(['comment' => $commentDetail->comment]),
            'PATCH'
        );
    }

    /**
     *
     */
    public function delete() {
        $this->api->run(
            $this->getUri(),
            null,
            null,
            'DELETE'
        );
    }
}
