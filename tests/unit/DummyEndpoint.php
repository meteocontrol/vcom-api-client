<?php

namespace meteocontrol\client\vcomapi\tests\unit;

use meteocontrol\client\vcomapi\endpoints\Endpoint;

class DummyEndpoint extends Endpoint {

    /**
     * @return string
     */
    public function getUri(): string {
        return "/dummy";
    }

    public function someActionThatGetsAJsonResponse() {
        return $this->jsonDecode("not a valid json string");
    }
}
