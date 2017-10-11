<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\tickets;

use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\client\vcomapi\model\Comment;
use meteocontrol\client\vcomapi\model\CommentDetail;

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
    public function get() {
        $commentsJson = $this->api->run($this->getUri());
        $decodedJson = json_decode($commentsJson, true);
        return Comment::deserializeArray($decodedJson['data']);
    }

    /**
     * @param CommentDetail $commentDetail
     * @return int commentId
     */
    public function create(CommentDetail $commentDetail) {
        if (!$commentDetail || !$commentDetail->isValid()) {
            throw new \InvalidArgumentException('Comment is invalid!');
        }
        $createdAt = $commentDetail->createdAt;
        $body = ['comment' => $commentDetail->comment];

        if ($createdAt !== null) {
            $body['createdAt'] = $createdAt->format(\DateTime::ATOM);
        }

        $responseBody = $this->api->run(
            $this->getUri(),
            null,
            json_encode($body),
            'POST'
        );
        return json_decode($responseBody)->data->commentId;
    }
}
