<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\systems;

use meteocontrol\vcomapi\model\User;
use meteocontrol\vcomapi\model\UserDetail;
use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\client\vcomapi\filters\UserCriteria;

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
     * @throws \meteocontrol\client\vcomapi\ApiClientException
     */
    public function get(UserCriteria $criteria = null) {
        if (is_null($criteria)) {
            $userListJson = $this->api->run($this->getUri());
            $decodedJson = json_decode($userListJson, true);
            return User::deserializeArray($decodedJson['data']);
        } else {
            $userDetailJson = $this->api->run($this->getUri(), $criteria->generateQueryString());
            $decodedJson = json_decode($userDetailJson, true);
            return UserDetail::deserialize($decodedJson['data']);
        }
    }
}
