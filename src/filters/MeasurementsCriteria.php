<?php

namespace meteocontrol\client\vcomapi\filters;

use DateTime;
use meteocontrol\client\vcomapi\readers\CsvFormat;
use UnexpectedValueException;

class MeasurementsCriteria {

    const RESOLUTION_MINUTE = 'minute';
    const RESOLUTION_INTERVAL = 'interval';
    const RESOLUTION_FIFTEEN_MINUTES = 'fifteen-minutes';
    const RESOLUTION_THIRTY_MINUTES = 'thirty-minutes';
    const RESOLUTION_HOUR = 'hour';
    const RESOLUTION_DAY = 'day';
    const RESOLUTION_MONTH = 'month';
    const RESOLUTION_YEAR = 'year';

    /** @var string[] */
    protected $filters = [];

    public function getDateFrom(): DateTime {
        return DateTime::createFromFormat(DATE_ATOM, $this->filters['from']);
    }

    public function withDateFrom(DateTime $from): self {
        $this->filters['from'] = $from->format(DATE_ATOM);
        return $this;
    }

    public function getDateTo(): DateTime {
        return DateTime::createFromFormat(DATE_ATOM, $this->filters['to']);
    }

    public function withDateTo(DateTime $to): self {
        $this->filters['to'] = $to->format(DATE_ATOM);
        return $this;
    }

    /**
     * @return string MeasurementsCriteria::RESOLUTION_INTERVAL
     *              | MeasurementsCriteria::RESOLUTION_MINUTE
     *              | MeasurementsCriteria::RESOLUTION_FIFTEEN_MINUTES
     *              | MeasurementsCriteria::RESOLUTION_THIRTY_MINUTES
     *              | MeasurementsCriteria::RESOLUTION_HOUR
     *              | MeasurementsCriteria::RESOLUTION_DAY
     *              | MeasurementsCriteria::RESOLUTION_MONTH
     *              | MeasurementsCriteria::RESOLUTION_YEAR
     *              | null
     */
    public function getResolution(): ?string {
        return $this->filters['resolution'] ?? null;
    }

    /**
     * @param string $resolution MeasurementsCriteria::RESOLUTION_INTERVAL
     *              | MeasurementsCriteria::RESOLUTION_MINUTE
     *              | MeasurementsCriteria::RESOLUTION_FIFTEEN_MINUTES
     *              | MeasurementsCriteria::RESOLUTION_THIRTY_MINUTES
     *              | MeasurementsCriteria::RESOLUTION_HOUR
     *              | MeasurementsCriteria::RESOLUTION_DAY
     *              | MeasurementsCriteria::RESOLUTION_MONTH
     *              | MeasurementsCriteria::RESOLUTION_YEAR
     */
    public function withResolution(string $resolution): self {
        $this->filters['resolution'] = $resolution;
        return $this;
    }

    public function getFormat(): string {
        return $this->filters['format'] ?? CsvFormat::FORMAT_JSON;
    }

    public function withFormat(string $format): self {
        $this->filters['format'] = $format;
        return $this;
    }

    public function withLineBreak(string $breakSymbol): self {
        $this->filters['lineBreak'] = $breakSymbol;
        return $this;
    }

    public function getDelimiter(): string {
        return $this->filters['delimiter'] ?? CsvFormat::DELIMITER_COMMA;
    }

    public function withDelimiter(string $delimiter): self {
        $this->filters['delimiter'] = $delimiter;
        return $this;
    }

    public function getDecimalPoint(): string {
        return $this->filters['decimalPoint'] ?? CsvFormat::DECIMAL_POINT_DOT;
    }

    public function withDecimalPoint(string $decimalPoint): self {
        $this->filters['decimalPoint'] = $decimalPoint;
        return $this;
    }

    public function withEmptyPlaceholder(string $emptyPlaceholder): self {
        $this->filters['emptyPlaceholder'] = $emptyPlaceholder;
        return $this;
    }

    public function withPrecision(int $precision): self {
        $this->filters['precision'] = $precision;
        return $this;
    }

    public function getIntervalIncluded(): bool {
        return isset($this->filters['includeInterval']);
    }

    public function withIntervalIncluded(): self {
        $this->filters['includeInterval'] = '1';
        return $this;
    }

    public function withActiveOnly(): self {
        $this->filters['activeOnly'] = '1';
        return $this;
    }

    public function withDeviceIds(array $deviceIds): self {
        $this->filters['deviceIds'] = implode(',', $deviceIds);
        return $this;
    }

    public function withAbbreviation(array $abbreviations): self {
        $this->filters['abbreviations'] = implode(',', $abbreviations);
        return $this;
    }

    public function withConsiderPowerControl(bool $considerPowerControl): self {
        $this->filters['considerPowerControl'] = $considerPowerControl;
        return $this;
    }

    public function generateQueryString(): string {
        $this->validateCriteriaSettings();
        $data = array_map(function ($value) {
            if (is_bool($value)) {
                return $value ? 'true' : 'false';
            }
            return $value;
        }, $this->filters);
        return http_build_query($data);
    }

    /**
     * @throws UnexpectedValueException
     */
    private function validateCriteriaSettings(): void {
        if ($this->getFormat() == CsvFormat::FORMAT_CSV) {
            if ($this->getDelimiter() == $this->getDecimalPoint()) {
                throw new UnexpectedValueException("Delimiter and decimal point symbols can't be the same");
            }
        }
    }
}
