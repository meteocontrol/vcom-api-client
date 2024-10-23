<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\tickets;

use GuzzleHttp\RequestOptions;
use InvalidArgumentException;
use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\client\vcomapi\model\CommentDetail;

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
        $meterJson = $this->api->get($this->getUri());
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
        $this->api->patch($this->getUri(), [RequestOptions::JSON => ['comment' => $commentDetail->comment]]);
    }

    /**
     * @return void
     */
    public function delete(): void {
        $this->api->delete($this->getUri());
    }
}
