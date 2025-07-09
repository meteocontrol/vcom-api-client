<?php

namespace unit\cmms;

use DateTime;
use GuzzleHttp\RequestOptions;
use meteocontrol\client\vcomapi\filters\SiteAccessCriteria;
use meteocontrol\client\vcomapi\model\SiteAccess;
use meteocontrol\client\vcomapi\model\System;
use meteocontrol\client\vcomapi\tests\unit\TestCase;

class SiteAccessTest extends TestCase {

    public function testGetSiteAccess() {
        $json = file_get_contents(__DIR__ . "/responses/getSiteAccess.json");
        $siteAccessCriteria = (new SiteAccessCriteria())
            ->withSystemKey("ABCDE")
            ->withStatus(SiteAccess::STATUS_UNREGISTERED)
            ->withDateCheckIn(DateTime::createFromFormat(DATE_ATOM, "2025-04-10T11:06:09+02:00"))
            ->withDateCheckOut(DateTime::createFromFormat(DATE_ATOM, "2025-04-16T11:06:09+02:00"));

        $this->api->expects($this->once())
            ->method("get")
            ->with(
                $this->identicalTo("cmms/site-access"),
                $this->identicalToUrl([
                    RequestOptions::QUERY => "systemKey=ABCDE" .
                        "&status=unregistered" .
                        "&checkIn=2025-04-10T11:06:09+02:00" .
                        "&checkOut=2025-04-16T11:06:09+02:00",
                ]),
            )
            ->willReturn($json);

        $result = $this->api->cmms()->siteAccess()->get($siteAccessCriteria);

        $this->assertEquals($this->getExpectedSiteAccess(), $result);
    }

    public function testGetSiteAccessWithMultipleParameters() {
        $json = file_get_contents(__DIR__ . "/responses/getSiteAccessWithMultipleParams.json");
        $siteAccessCriteria = (new SiteAccessCriteria())
            ->withSystemKey(["ABCDE", "FGHIJ"])
            ->withStatus([SiteAccess::STATUS_REGISTERED, SiteAccess::STATUS_UNREGISTERED])
            ->withDateCheckIn(DateTime::createFromFormat(DATE_ATOM, "2025-04-12T11:14:30+02:00"))
            ->withDateCheckOut(DateTime::createFromFormat(DATE_ATOM, "2025-04-16T11:14:30+02:00"));

        $this->api->expects($this->once())
            ->method("get")
            ->with(
                $this->identicalTo("cmms/site-access"),
                $this->identicalToUrl([
                    RequestOptions::QUERY => "systemKey=ABCDE,FGHIJ" .
                        "&status=registered,unregistered" .
                        "&checkIn=2025-04-12T11:14:30+02:00" .
                        "&checkOut=2025-04-16T11:14:30+02:00",
                ]),
            )
            ->willReturn($json);

        $result = $this->api->cmms()->siteAccess()->get($siteAccessCriteria);

        $this->assertEquals($this->getExpectedMultipleSiteAccess(), $result);
    }

    /**
     * @return SiteAccess[]
     */
    private function getExpectedSiteAccess(): array {
        $siteAccessModel = new SiteAccess();
        $system = new System();
        $system->key = "ABCDE";
        $system->name = "System Name";
        $siteAccessModel->system = $system;
        $siteAccessModel->name = "site access name";
        $siteAccessModel->status = SiteAccess::STATUS_UNREGISTERED;
        $siteAccessModel->comment = "comment";
        $siteAccessModel->checkIn = new DateTime("2025-04-10T11:06:09+02:00");
        $siteAccessModel->checkOut = new DateTime("2025-04-16T11:06:09+02:00");

        return [$siteAccessModel];
    }

    private function getExpectedMultipleSiteAccess(): array {
        $siteAccessModel1 = new SiteAccess();
        $siteAccessModel2 = new SiteAccess();
        $system1 = new System();
        $system1->key = "ABCDE";
        $system1->name = "System Name1";
        $siteAccessModel1->system = $system1;
        $siteAccessModel1->status = SiteAccess::STATUS_UNREGISTERED;
        $siteAccessModel1->name = "site access name";
        $siteAccessModel1->comment = null;
        $siteAccessModel1->checkIn =  new DateTime("2025-04-12T11:14:30+02:00");
        $siteAccessModel1->checkOut = new DateTime("2025-04-16T11:14:30+02:00");
        $system2 = new System();
        $system2->key = "FGHIJ";
        $system2->name = "System Name2";
        $siteAccessModel2->system = $system2;
        $siteAccessModel2->status = SiteAccess::STATUS_REGISTERED;
        $siteAccessModel2->name = "site access name1";
        $siteAccessModel2->comment = "comment";
        $siteAccessModel2->checkIn =  new DateTime("2025-04-12T11:14:30+02:00");
        $siteAccessModel2->checkOut = new DateTime("2025-04-16T11:14:30+02:00");

        return [$siteAccessModel1, $siteAccessModel2];
    }
}
