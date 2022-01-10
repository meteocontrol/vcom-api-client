<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\tickets;

use DateTime;
use InvalidArgumentException;
use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\vcomapi\model\Comment;
use meteocontrol\vcomapi\model\CommentDetail;

class Comments extends SubEndpoint {

    /**
     * @param EndpointInterface $parent
     */
    public function __construct(EndpointInterface $parent) {
        $this->uri = '/comments';
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    /**
     * @return Comment[]
     */
    public function get(): array {
        $commentsJson = $this->api->run($this->getUri());
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
            $body['createdAt'] = $createdAt->format(DateTime::ATOM);
        }

        $responseBody = $this->api->run(
            $this->getUri(),
            null,
            json_encode($body),
            'POST'
        );
        return $this->jsonDecode($responseBody)->data->commentId;
    }
}
