<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\systems;

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
