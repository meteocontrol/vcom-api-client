<?php

namespace meteocontrol\client\vcomapi\readers;

use meteocontrol\client\vcomapi\filters\MeasurementsCriteria;
use meteocontrol\client\vcomapi\writer\Writer;

class MeasurementsBulkReader {

    /** @var array */
    private $delimiterMapping = [
        "comma" => ",",
        "semicolon" => ";",
        "colon" => ":",
        "tab" => "\t",
    ];

    /** @var string */
    private $rawData;
    /** @var MeasurementsCriteria */
    private $criteria;

    /**
     * @param $raw
     * @param MeasurementsCriteria|null $criteria
     */
    public function __construct($raw, MeasurementsCriteria $criteria = null) {
        $this->rawData = $raw;
        $this->criteria = $criteria;
    }

    /**
     * @return string
     */
    public function getAsString() {
        return $this->rawData;
    }

    /**
     * @return array
     */
    public function getAsArray() {
        if ($this->isReturnCsvFormat()) {
            return str_getcsv($this->rawData, $this->getDelimiterSymbol($this->criteria->getDelimiter()));
        } else {
            return json_decode($this->rawData, true);
        }
    }

    /**
     * @param Writer $writer
     */
    public function write(Writer $writer) {
        $writer->write($this->getAsString());
    }

    /**
     * @return bool
     */
    private function isReturnCsvFormat() {
        return !is_null($this->criteria)
        && $this->criteria->getFormat() == CsvFormat::FORMAT_CSV;
    }

    /**
     * @param $delimiterName
     * @return string
     */
    private function getDelimiterSymbol($delimiterName) {
        return $this->delimiterMapping[$delimiterName];
    }
}
