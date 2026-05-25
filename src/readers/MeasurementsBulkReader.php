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

    public function __construct(string $raw, ?MeasurementsCriteria $criteria = null) {
        $this->rawData = $raw;
        $this->criteria = $criteria;
    }

    public function getAsString(): string {
        return $this->rawData;
    }

    public function getAsArray(): array {
        if ($this->isReturnCsvFormat()) {
            return str_getcsv($this->rawData, $this->getDelimiterSymbol($this->criteria->getDelimiter()));
        }
        return json_decode($this->rawData, true);
    }

    public function write(Writer $writer): void {
        $writer->write($this->getAsString());
    }

    private function isReturnCsvFormat(): bool {
        return !is_null($this->criteria)
        && $this->criteria->getFormat() == CsvFormat::FORMAT_CSV;
    }

    private function getDelimiterSymbol(string $delimiterName): string {
        return $this->delimiterMapping[$delimiterName];
    }
}
