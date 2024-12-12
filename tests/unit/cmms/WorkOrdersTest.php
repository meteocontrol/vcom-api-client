<?php

namespace meteocontrol\client\vcomapi\tests\unit\cmms;

use DateTime;
use meteocontrol\client\vcomapi\filters\SystemCriteria;
use meteocontrol\client\vcomapi\tests\unit\TestCase;
use meteocontrol\client\vcomapi\model\WorkOrder;

class WorkOrdersTest extends TestCase {

    public function testGetWorkorders() {
        $json = file_get_contents(__DIR__ . "/responses/getWorkorders.json");
        $systemCriteria = (new SystemCriteria())
            ->withSystemKey('ABCDE');

        $this->api->expects($this->once())
            ->method("get")
            ->with($this->identicalTo("cmms/workorders"))
            ->willReturn($json);

        $actualResults = $this->api->cmms()->workOrders()->get($systemCriteria);

        $this->assertEquals($this->getExpectedWorkorders(), $actualResults);
    }

    /**
     * @return WorkOrder[]
     */
    private function getExpectedWorkorders(): array {
        $workOrder1 = new WorkOrder();
        $workOrder1->workOrderId = 12145;
        $workOrder1->title = "WR tausch";
        $workOrder1->lastChangedAt = new DateTime("2020-06-10T11:06:09Z");

        $workOrder2 = new WorkOrder();
        $workOrder2->workOrderId = 12326;
        $workOrder2->title = "Der Auftrag";
        $workOrder2->lastChangedAt = new DateTime("2020-07-08T10:15:36Z");

        return [$workOrder1, $workOrder2];
    }
}
