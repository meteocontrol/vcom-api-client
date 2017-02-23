<?php

namespace meteocontrol\client\vcomapi\tests\unit;

use meteocontrol\client\vcomapi\ApiClient;
use meteocontrol\client\vcomapi\endpoints\Endpoint;
use meteocontrol\client\vcomapi\endpoints\main\Systems;
use meteocontrol\client\vcomapi\endpoints\main\Tickets;
use meteocontrol\client\vcomapi\endpoints\sub\AbbreviationId;
use meteocontrol\client\vcomapi\endpoints\sub\systems\Abbreviations;
use meteocontrol\client\vcomapi\endpoints\sub\systems\Measurements;
use meteocontrol\client\vcomapi\endpoints\sub\systems\SystemId;

class EndpointTest extends \PHPUnit_Framework_TestCase {

    /** @var ApiClient */
    private $apiClient;

    /** @var Endpoint */
    private $endpoint;

    public function setUp() {
        $this->apiClient = $this->getMockBuilder('\meteocontrol\client\vcomapi\ApiClient')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testUri() {
        $this->endpoint = new Systems($this->apiClient);
        $this->assertEquals('systems', $this->endpoint->getUri());

        $this->endpoint = new Tickets($this->apiClient);
        $this->assertEquals('tickets', $this->endpoint->getUri());
    }

    public function testSetSubEndpoint() {
        $systemsEndpoint = new Systems($this->apiClient);
        $systemIdEndpoint = new SystemId($systemsEndpoint, 'ABCDE');
        $abbreviationsEndpoint = new Abbreviations($systemIdEndpoint);
        $abbreviationIdEndpoint = new AbbreviationId($abbreviationsEndpoint, 'E_INT');
        $measurementEndpoint = new Measurements($abbreviationIdEndpoint);

        $this->assertEquals(
            'systems/ABCDE/abbreviations/E_INT/measurements',
            $measurementEndpoint->getUri()
        );
    }
}
