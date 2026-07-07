<?php

namespace meteocontrol\client\vcomapi\tests\unit\cmms;

use DateTime;
use GuzzleHttp\RequestOptions;
use meteocontrol\client\vcomapi\filters\SystemCriteria;
use meteocontrol\client\vcomapi\model\CmmsAdditionalAssignee;
use meteocontrol\client\vcomapi\model\CmmsAssignee;
use meteocontrol\client\vcomapi\model\WorkOrderDetail;
use meteocontrol\client\vcomapi\tests\unit\TestCase;

class WorkOrderTest extends TestCase {

    public function testGetWorkOrder() {
        $orderId = 12326;
        $json = file_get_contents(__DIR__ . '/responses/getWorkOrder.json');
        $systemCriteria = (new SystemCriteria())
            ->withTimezone('UTC');

        $this->api->expects($this->once())
            ->method('get')
            ->with(
                $this->identicalTo("cmms/workorders/{$orderId}"),
                $this->identicalToUrl([
                    RequestOptions::QUERY => 'timezone=UTC',
                ]),
            )
            ->willReturn($json);

        $actualResult = $this->api->cmms()->workOrder($orderId)->get($systemCriteria);
        $this->assertEquals($this->getExpectedWorkOrder(), $actualResult);
    }

    public function testGetWorkOrderWithAdditionalAssignees() {
        $orderId = 12326;
        $json = file_get_contents(__DIR__ . '/responses/getWorkOrderWithAdditionalAssignees.json');
        $systemCriteria = (new SystemCriteria())
            ->withTimezone('UTC');

        $this->api->expects($this->once())
            ->method('get')
            ->with(
                $this->identicalTo("cmms/workorders/{$orderId}"),
                $this->identicalToUrl([
                    RequestOptions::QUERY => 'timezone=UTC',
                ]),
            )
            ->willReturn($json);

        $actualResult = $this->api->cmms()->workOrder($orderId)->get($systemCriteria);
        $this->assertEquals(
            $this->getExpectedWorkOrder($this->createMultipleAssignees()),
            $actualResult,
        );
    }

    private function getExpectedWorkOrder(array $additionalAssignees = []): WorkOrderDetail {
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
        $workOrder->additionalAssignees = $additionalAssignees;

        return $workOrder;
    }

    /**
     * @return CmmsAdditionalAssignee[]
     */
    private function createMultipleAssignees(): array {
        $assignee1 = new CmmsAdditionalAssignee();
        $assignee1->username = 'additional-user-1';
        $assignee1->id = "1";

        $assignee2 = new CmmsAdditionalAssignee();
        $assignee2->username = 'additional-user-2';
        $assignee2->id = "2";

        $assignee3 = new CmmsAdditionalAssignee();
        $assignee3->username = 'additional-user-3';
        $assignee3->id = "3";

        return [$assignee1, $assignee2, $assignee3];
    }
}
