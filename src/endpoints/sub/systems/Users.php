<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\systems;

use GuzzleHttp\RequestOptions;
use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\client\vcomapi\filters\UserCriteria;
use meteocontrol\client\vcomapi\model\User;
use meteocontrol\client\vcomapi\model\UserDetail;

class Users extends SubEndpoint {

    /**
     * @param EndpointInterface $parent
     */
    public function __construct(EndpointInterface $parent) {
        $this->uri = '/users';
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    /**
     * @param UserCriteria|null $criteria
     * @return User[] | UserDetail
     */
    public function get(UserCriteria $criteria = null) {
        if (is_null($criteria)) {
            $userListJson = $this->api->get($this->getUri());
            return User::deserializeArray($this->jsonDecode($userListJson, true)['data']);
        } else {
            $userDetailJson = $this->api->get(
                $this->getUri(),
                [RequestOptions::QUERY => $criteria->generateQueryString()]
            );
            return UserDetail::deserialize($this->jsonDecode($userDetailJson, true)['data']);
        }
    }
}
