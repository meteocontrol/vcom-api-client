<?php

namespace meteocontrol\client\vcomapi\filters;

class SystemCriteria {

    /** @var string[] */
    private $filters = [];

    public function withSystemKey(string $systemKey): self {
        $this->filters['systemKey'] = $systemKey;
        return $this;
    }

    public function withTimezone(string $timezone): self {
        $this->filters['timezone'] = $timezone;
        return $this;
    }

    public function withTags($tag): self {
        $this->filters['tags'] = is_array($tag) ? implode(',', $tag) : $tag;
        return $this;
    }

    public function generateQueryString(): string {
        return http_build_query($this->filters);
    }
}
