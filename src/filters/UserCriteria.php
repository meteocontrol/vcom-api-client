<?php

namespace meteocontrol\client\vcomapi\filters;

class UserCriteria {

    /** @var string[] */
    private $filters;

    /**
     * @return string
     */
    public function getUsername() {
        return $this->filters['username'];
    }

    /**
     * @param string $username
     * @return $this
     */
    public function withUsername($username) {
        $this->filters['username'] = $username;
        return $this;
    }

    /**
     * @return string
     */
    public function generateQueryString() {
        return http_build_query($this->filters);
    }
}
