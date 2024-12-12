<?php

namespace meteocontrol\client\vcomapi\filters;

class ForecastCriteria extends MeasurementsCriteria {

    const CATEGORY_DAY_AHEAD = 'dayAhead';
    const CATEGORY_INTRADAY = 'intraday';
    const CATEGORY_INTRADAY_OPTIMIZED = 'intradayOptimized';

    /**
     * @param int $hours
     * @return $this
     */
    public function withHoursToFuture(int $hours): self {
        $this->filters['hours_to_future'] = $hours;
        return $this;
    }

    /**
     * @param string $timezone
     * @return $this
     */
    public function withTimezone(string $timezone): self {
        $this->filters['timezone'] = $timezone;
        return $this;
    }

    /**
     * @return string
     */
    public function getResolution(): string {
        return parent::getResolution() ?? self::RESOLUTION_FIFTEEN_MINUTES;
    }

    /**
     * @param string $category ForecastCriteria::CATEGORY_DAY_AHEAD
     *              | ForecastCriteria::CATEGORY_INTRADAY
     *              | ForecastCriteria::CATEGORY_INTRADAY_OPTIMIZED
     * @return MeasurementsCriteria
     */
    public function withCategory(string $category): self {
        $this->filters['category'] = $category;
        return $this;
    }
}
