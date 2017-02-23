<?php

namespace meteocontrol\client\vcomapi\tests\unit;

use meteocontrol\client\vcomapi\ApiClient;
use meteocontrol\client\vcomapi\endpoints\main\MainEndpoint;
use meteocontrol\client\vcomapi\endpoints\main\Systems;
use meteocontrol\client\vcomapi\endpoints\sub\AbbreviationId;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\client\vcomapi\endpoints\sub\systems\Abbreviations;
use meteocontrol\client\vcomapi\endpoints\sub\systems\Basics;
use meteocontrol\client\vcomapi\endpoints\sub\systems\Calculations;
use meteocontrol\client\vcomapi\endpoints\sub\systems\DeviceId;
use meteocontrol\client\vcomapi\endpoints\sub\systems\Inverters;
use meteocontrol\client\vcomapi\endpoints\sub\systems\Measurements;
use meteocontrol\client\vcomapi\endpoints\sub\systems\Sensors;
use meteocontrol\client\vcomapi\endpoints\sub\systems\Meters;
use meteocontrol\client\vcomapi\endpoints\sub\systems\SystemId;
use meteocontrol\client\vcomapi\endpoints\sub\systems\TechnicalData;
use meteocontrol\client\vcomapi\endpoints\sub\tickets\CommentId;
use meteocontrol\client\vcomapi\endpoints\sub\tickets\Comments;
use meteocontrol\client\vcomapi\endpoints\sub\tickets\TicketId;

class SubEndpointTest extends \PHPUnit_Framework_TestCase {

    /** @var ApiClient */
    private $apiClient;

    /** @var SubEndpoint */
    private $subEndpoint;

    /** @var MainEndpoint */
    private $mainEndpoint;

    public function setUp() {
        $this->apiClient = $this->getMockBuilder('\meteocontrol\client\vcomapi\ApiClient')
            ->disableOriginalConstructor()
            ->getMock();
        $this->mainEndpoint = new Systems($this->apiClient);
    }

    public function testUri() {
        $this->subEndpoint = new AbbreviationId($this->mainEndpoint, 1);
        $this->assertEquals('systems/1', $this->subEndpoint->getUri());

        $this->subEndpoint = new Abbreviations($this->mainEndpoint);
        $this->assertEquals('systems/abbreviations', $this->subEndpoint->getUri());

        $this->subEndpoint = new Basics($this->mainEndpoint);
        $this->assertEquals('systems/basics', $this->subEndpoint->getUri());

        $this->subEndpoint = new Calculations($this->mainEndpoint);
        $this->assertEquals('systems/calculations', $this->subEndpoint->getUri());

        $this->subEndpoint = new CommentId($this->mainEndpoint, 1);
        $this->assertEquals('systems/1', $this->subEndpoint->getUri());

        $this->subEndpoint = new Comments($this->mainEndpoint);
        $this->assertEquals('systems/comments', $this->subEndpoint->getUri());

        $this->subEndpoint = new DeviceId($this->mainEndpoint, 1);
        $this->assertEquals('systems/1', $this->subEndpoint->getUri());

        $this->subEndpoint = new Inverters($this->mainEndpoint);
        $this->assertEquals('systems/inverters', $this->subEndpoint->getUri());

        $this->subEndpoint = new Measurements($this->mainEndpoint);
        $this->assertEquals('systems/measurements', $this->subEndpoint->getUri());

        $this->subEndpoint = new Sensors($this->mainEndpoint);
        $this->assertEquals('systems/sensors', $this->subEndpoint->getUri());

        $this->subEndpoint = new Meters($this->mainEndpoint);
        $this->assertEquals('systems/meters', $this->subEndpoint->getUri());

        $this->subEndpoint = new SystemId($this->mainEndpoint, 1);
        $this->assertEquals('systems/1', $this->subEndpoint->getUri());

        $this->subEndpoint = new TechnicalData($this->mainEndpoint);
        $this->assertEquals('systems/technical-data', $this->subEndpoint->getUri());

        $this->subEndpoint = new TicketId($this->mainEndpoint, 1);
        $this->assertEquals('systems/1', $this->subEndpoint->getUri());
    }
}
