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
     * @param string $raw
     * @param MeasurementsCriteria|null $criteria
     */
    public function __construct(string $raw, MeasurementsCriteria $criteria = null) {
        $this->rawData = $raw;
        $this->criteria = $criteria;
    }

    /**
     * @return string
     */
    public function getAsString(): string {
        return $this->rawData;
    }

    /**
     * @return array
     */
    public function getAsArray(): array {
        if ($this->isReturnCsvFormat()) {
            return str_getcsv($this->rawData, $this->getDelimiterSymbol($this->criteria->getDelimiter()));
        }
        return json_decode($this->rawData, true);
    }

    /**
     * @param Writer $writer
     * @return void
     */
    public function write(Writer $writer): void {
        $writer->write($this->getAsString());
    }

    /**
     * @return bool
     */
    private function isReturnCsvFormat(): bool {
        return !is_null($this->criteria)
        && $this->criteria->getFormat() == CsvFormat::FORMAT_CSV;
    }

    /**
     * @param string $delimiterName
     * @return string
     */
    private function getDelimiterSymbol(string $delimiterName): string {
        return $this->delimiterMapping[$delimiterName];
    }
}
