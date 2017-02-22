<?php
namespace meteocontrol\client\vcomapi\writer;

interface Writer {

    /**
     * @param string $data
     * @return mixed
     */
    public function write($data);
}
