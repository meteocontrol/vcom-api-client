<?php
namespace meteocontrol\client\vcomapi\writer;

interface Writer {

    /**
     * @param string $data
     * @return void
     */
    public function write(string $data): void;
}
