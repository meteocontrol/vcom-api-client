<?php

namespace meteocontrol\client\vcomapi\filters;

class UserCriteria {

    /** @var string[] */
    private $filters;

    /**
     * @return string
     */
    public function getUsername(): string {
        return $this->filters['username'];
    }

    /**
     * @param string $username
     * @return $this
     */
    public function withUsername(string $username): self {
        $this->filters['username'] = $username;
        return $this;
    }

    /**
     * @return string
     */
    public function generateQueryString(): string {
        return http_build_query($this->filters);
    }
}
