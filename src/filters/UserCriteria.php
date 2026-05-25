<?php

namespace meteocontrol\client\vcomapi\filters;

class UserCriteria {

    /** @var string[] */
    private $filters = [];

    public function withUsername(string $username): self {
        $this->filters['username'] = $username;
        return $this;
    }

    public function generateQueryString(): string {
        return http_build_query($this->filters);
    }
}
