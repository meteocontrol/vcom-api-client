<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\tickets;

use GuzzleHttp\RequestOptions;
use InvalidArgumentException;
use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\client\vcomapi\filters\TicketsCriteria;
use meteocontrol\client\vcomapi\model\Comment;
use meteocontrol\client\vcomapi\model\CommentDetail;

class Comments extends SubEndpoint {

    public function __construct(EndpointInterface $parent) {
        $this->uri = '/comments';
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    public function get(?TicketsCriteria $criteria = null): array {
        $options = [];
        if ($criteria) {
            $options = [RequestOptions::QUERY => $criteria->generateQueryString()];
        }
        $commentsJson = $this->api->get($this->getUri(), $options);
        return Comment::deserializeArray($this->jsonDecode($commentsJson, true)['data']);
    }

    /**
     * @param CommentDetail $commentDetail
     * @return int
     * @throws InvalidArgumentException
     */
    public function create(CommentDetail $commentDetail): int {
        if (!$commentDetail || !$commentDetail->isValid()) {
            throw new InvalidArgumentException('Comment is invalid!');
        }
        $createdAt = $commentDetail->createdAt;
        $body = ['comment' => $commentDetail->comment];

        if ($createdAt !== null) {
            $body['createdAt'] = $createdAt->format(DATE_ATOM);
        }

        $responseBody = $this->api->post($this->getUri(), [RequestOptions::JSON => $body]);
        return $this->jsonDecode($responseBody)->data->commentId;
    }
}
