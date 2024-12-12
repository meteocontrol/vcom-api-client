<?php

namespace meteocontrol\client\vcomapi\filters;

class SystemCriteria {

    /** @var string[] */
    private $filters = [];

    /**
     * @param string $systemKey
     * @return $this
     */
    public function withSystemKey(string $systemKey): self {
        $this->filters['systemKey'] = $systemKey;
        return $this;
    }

    /**
     * @return string
     */
    public function generateQueryString(): string {
        return http_build_query($this->filters);
    }
}
