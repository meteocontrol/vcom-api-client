<?php

namespace meteocontrol\client\vcomapi\filters;

use meteocontrol\client\vcomapi\readers\CsvFormat;

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
    private $filters;

    /**
     * @return \DateTime
     */
    public function getDateFrom() {
        return \DateTime::createFromFormat(\DateTime::RFC3339, $this->filters['from']);
    }

    /**
     * @param \DateTime $from
     * @return MeasurementsCriteria
     */
    public function withDateFrom(\DateTime $from) {
        $this->filters['from'] = $from->format(\DateTime::RFC3339);
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateTo() {
        return \DateTime::createFromFormat(\DateTime::RFC3339, $this->filters['to']);
    }

    /**
     * @param \DateTime $to
     * @return MeasurementsCriteria
     */
    public function withDateTo(\DateTime $to) {
        $this->filters['to'] = $to->format(\DateTime::RFC3339);
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
    public function getResolution() {
        return isset($this->filters['resolution']) ? $this->filters['resolution'] : null;
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
     * @return MeasurementsCriteria
     */
    public function withResolution($resolution) {
        $this->filters['resolution'] = $resolution;
        return $this;
    }

    /**
     * @return string
     */
    public function getFormat() {
        return isset($this->filters['format']) ? $this->filters['format'] : CsvFormat::FORMAT_JSON;
    }

    /**
     * @param string $format
     * @return MeasurementsCriteria
     */
    public function withFormat($format) {
        $this->filters['format'] = $format;
        return $this;
    }

    /**
     * @return string
     */
    public function getLineBreak() {
        return isset($this->filters['lineBreak']) ? $this->filters['lineBreak'] : CsvFormat::LINE_BREAK_LF;
    }

    /**
     * @param string $breakSymbol
     * @return MeasurementsCriteria
     */
    public function withLineBreak($breakSymbol) {
        $this->filters['lineBreak'] = $breakSymbol;
        return $this;
    }

    /**
     * @return string
     */
    public function getDelimiter() {
        return isset($this->filters['delimiter']) ? $this->filters['delimiter'] : CsvFormat::DELIMITER_COMMA;
    }

    /**
     * @param string $delimiter
     * @return MeasurementsCriteria
     */
    public function withDelimiter($delimiter) {
        $this->filters['delimiter'] = $delimiter;
        return $this;
    }

    /**
     * @return string
     */
    public function getDecimalPoint() {
        return isset($this->filters['decimalPoint']) ?
            $this->filters['decimalPoint'] :
            CsvFormat::DECIMAL_POINT_DOT;
    }

    /**
     * @param string $decimalPoint
     * @return MeasurementsCriteria
     */
    public function withDecimalPoint($decimalPoint) {
        $this->filters['decimalPoint'] = $decimalPoint;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmptyPlaceholder() {
        return isset($this->filters['emptyPlaceholder']) ?
            $this->filters['emptyPlaceholder'] :
            CsvFormat::EMPTY_PLACE_HOLDER_EMPTY;
    }

    /**
     * @param string $emptyPlaceholder
     * @return MeasurementsCriteria
     */
    public function withEmptyPlaceholder($emptyPlaceholder) {
        $this->filters['emptyPlaceholder'] = $emptyPlaceholder;
        return $this;
    }

    /**
     * @return int
     */
    public function getPrecision() {
        return isset($this->filters['precision']) ? $this->filters['precision'] : CsvFormat::PRECISION_2;
    }

    /**
     * @param int $precision
     * @return MeasurementsCriteria
     */
    public function withPrecision($precision) {
        $this->filters['precision'] = $precision;
        return $this;
    }

    /**
     * @return bool
     */
    public function getIntervalIncluded() {
        return isset($this->filters['includeInterval']);
    }

    /**
     * @return MeasurementsCriteria
     */
    public function withIntervalIncluded() {
        $this->filters['includeInterval'] = '1';
        return $this;
    }

    /**
     * @return string
     */
    public function generateQueryString() {
        $this->validateCriteriaSettings();
        return http_build_query($this->filters);
    }

    /**
     * @throws \UnexpectedValueException
     */
    private function validateCriteriaSettings() {
        if ($this->getFormat() == CsvFormat::FORMAT_CSV) {
            if ($this->getDelimiter() == $this->getDecimalPoint()) {
                throw new \UnexpectedValueException("Delimiter and decimal point symbols can't be the same");
            }
        }
    }
}
