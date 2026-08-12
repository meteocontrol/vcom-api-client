<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\tickets;

use GuzzleHttp\RequestOptions;
use InvalidArgumentException;
use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\client\vcomapi\filters\TicketsCriteria;
use meteocontrol\client\vcomapi\model\CommentDetail;

class Comment extends SubEndpoint {

    public function __construct(EndpointInterface $parent) {
        $this->uri = '';
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    public function get(?TicketsCriteria $criteria = null): CommentDetail {
        $options = [];
        if ($criteria) {
            $options = [RequestOptions::QUERY => $criteria->generateQueryString()];
        }
        $commentJson = $this->api->get($this->getUri(), $options);
        return CommentDetail::deserialize($this->jsonDecode($commentJson, true)['data']);
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

    public function delete(): void {
        $this->api->delete($this->getUri());
    }
}
