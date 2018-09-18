<?php

namespace meteocontrol\client\vcomapi\tests\unit;

use meteocontrol\client\vcomapi\endpoints\Endpoint;

class DummyEndpoint extends Endpoint {

    /**
     * @return string
     */
    public function getUri() {
        return "/dummy";
    }

    public function someActionThatGetsAJsonReponse() {
        return $this->jsonDecode("not a valid json string");
    }
}
