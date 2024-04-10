<?php

namespace meteocontrol\client\vcomapi\tests\unit\cmms;

use DateTime;
use meteocontrol\client\vcomapi\tests\unit\TestCase;
use meteocontrol\vcomapi\model\CmmsSystem;

class SystemTest extends TestCase {

    public function testGetSystems() {
        $json = file_get_contents(__DIR__ . "/responses/getSystems.json");
        $this->api->expects($this->once())
            ->method("run")
            ->with($this->identicalTo("cmms/systems"))
            ->willReturn($json);

        $actualSystems = $this->api->cmms()->systems()->get();
        $this->assertEquals($this->getExpectedSystems(), $actualSystems);
    }

    /**
     * @return CmmsSystem[]
     */
    private function getExpectedSystems(): array {
        $system1 = new CmmsSystem();
        $system1->activeUntil = new DateTime("2020-09-30");
        $system1->activeSince = new DateTime("2019-09-10");
        $system1->key = "7E3G5";
        $system1->name = "Solaris Real Ltd. I & II, P 31348";
        $system1->renew = 0;

        $system2 = new CmmsSystem();
        $system2->activeUntil = new DateTime("2020-12-31");
        $system2->activeSince = new DateTime("2019-09-10");
        $system2->key = "4DC11";
        $system2->name = "VCOM API E2E Test";
        $system2->renew = 1;

        $system3 = new CmmsSystem();
        $system3->key = "VUS56";
        $system3->name = "Test partner system";

        return [$system1, $system2, $system3];
    }
}
