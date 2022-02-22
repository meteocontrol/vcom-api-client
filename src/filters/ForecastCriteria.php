<?php

namespace meteocontrol\client\vcomapi\filters;

class ForecastCriteria extends MeasurementsCriteria {

    /**
     * @return int
     */
    public function getHoursToFuture(): int {
        return $this->filters['hours_to_future'] ?? 48;
    }

    /**
     * @param int $hours
     * @return $this
     */
    public function withHoursToFuture(int $hours): self {
        $this->filters['hours_to_future'] = $hours;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTimezone(): ?string {
        return $this->filters['timezone'] ?? null;
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
        return $this->filters['resolution'] ?? ForecastCriteria::RESOLUTION_FIFTEEN_MINUTES;
    }
}
