<?php

namespace meteocontrol\client\vcomapi\filters;

class PaginationCriteria extends MeasurementsCriteria {

    /** @var string[] */
    private $filters;

    /**
     * @return int
     */
    public function getSkip() {
        return isset($this->filters['skip']) ? $this->filters['skip'] : 0;
    }

    /**
     * @param int $skip
     * @return PaginationCriteria
     */
    public function withSkip($skip) {
        $this->filters['skip'] = (int)$skip;
        return $this;
    }

    /**
     * @return int
     */
    public function getLimit() {
        return isset($this->filters['limit']) ? $this->filters['limit'] : 2;
    }

    /**
     * @param int $limit
     * @return PaginationCriteria
     */
    public function withLimit($limit) {
        $this->filters['limit'] = (int)$limit;
        return $this;
    }

    /**
     * @return string
     */
    public function generateQueryString() {
        $append = '';
        if (isset($this->filters['skip']) || isset($this->filters['limit'])) {
            $append = '&' . http_build_query($this->filters);
        }
        return parent::generateQueryString() . $append;
    }
}
