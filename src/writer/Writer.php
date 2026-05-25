<?php
namespace meteocontrol\client\vcomapi\writer;

interface Writer {

    public function write(string $data): void;
}
