<?php

namespace meteocontrol\client\vcomapi\tests\unit\cmms;

use DateTime;
use meteocontrol\client\vcomapi\tests\unit\TestCase;
use meteocontrol\client\vcomapi\model\CmmsAssignee;
use meteocontrol\client\vcomapi\model\WorkOrderDetail;

class WorkorderTest extends TestCase {

    public function testGetWorkorder() {
        $orderId = 12326;
        $json = file_get_contents(__DIR__ . "/responses/getWorkorder.json");

        $this->api->expects($this->once())
            ->method("get")
            ->with($this->identicalTo("cmms/workorders/{$orderId}"))
            ->willReturn($json);
        $actualResult = $this->api->cmms()->workOrder($orderId)->get();
        $this->assertEquals($this->getExpectedWorkorder(), $actualResult);
    }

    /**
     * @return WorkOrderDetail
     */
    private function getExpectedWorkorder(): WorkOrderDetail {
        $workOrder = new WorkOrderDetail();
        $workOrder->workOrderId = 12326;
        $workOrder->ticketId = 158359;
        $workOrder->systemKey = '4DC11';
        $workOrder->title = 'Der Auftrag';
        $workOrder->description = 'Mach des jetz';
        $workOrder->status = 'processed';
        $workOrder->createdAt = new DateTime('2020-07-08T06:39:58Z');
        $workOrder->lastChangedAt = new DateTime('2020-07-08T10:15:36Z');
        $workOrder->assignee = new CmmsAssignee();
        $workOrder->assignee->username = 'vcom-api-e2e-test-user';
        $workOrder->assignee->status = 'accepted';
        $workOrder->assignee->statusDateAt = new DateTime('2020-07-08T06:49:07Z');
        return $workOrder;
    }
}
